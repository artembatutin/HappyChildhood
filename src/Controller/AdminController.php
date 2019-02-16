<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\AnnouncementViewers;
use App\Entity\Enrollment;
use App\Entity\Group;
use App\Entity\User;
use App\Form\EnrollmentForm;
use App\Form\GroupForm;
use App\Form\UserCreate;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Psr\Log\LoggerInterface;

class AdminController extends AbstractController {
	
	public function content(Request $request, $block_id = -1) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		$groups = $em->getRepository(Group::class)->findAll();
		
		
		$announcement = new Announcement();
		$mode = "Create";
		//edit mode.
		if($block_id != -1) {
			$repository = $this->getDoctrine()->getRepository(Announcement::class);
			$announcement = $repository->find($block_id);
			$mode = "Edit";
		}
		
		//creating the new announcement.
		$createForm = $this->createFormBuilder($announcement)
		                   ->add('title', TextType::class)
		                   ->add('message', TextareaType::class, ['label' => 'Content'])
		                   ->add('public', CheckboxType::class, ['label' => 'Announcement public',  'required' => false])
		                   ->add('hidden', CheckboxType::class, ['label' => 'Hide it temporary', 'required' => false])
		                   ->add('commenting', CheckboxType::class, ['label' => 'Allow comments',  'required' => false])
		                   ->add('pictures', FileType::class, ['required' => false, 'label' => 'Pictures', 'mapped' => false, 'multiple' => true])
		                   ->add('groups', ChoiceType::class, array( 'required' => false,
		                   	'choices' => $groups, 'choice_label' => function($grp, $key, $value) {
			                   return $grp->getName();
		                   }, 'choice_value' => function(Group $grp = null) {
			                   return $grp ? $grp->getId() : '';
		                   }, 'mapped' => false, 'multiple' => true))
		                   ->getForm();
		
		//request submitted.
		if($request->isMethod('POST')) {
			$createForm->handleRequest($request);
			if($createForm->isSubmitted() && $createForm->isValid()) {
				//creating announcement.
				$announcement = $createForm->getData();
				$announcement->setUser($this->getUser());
				try {
					$announcement->setCreationDate(new \DateTime());
				} catch(\Exception $e) {
					throw $e;
				}
				//uploading pictures.
				$pictures = $createForm->get('pictures')->getData();
				foreach($pictures as $picture) {
					$announcement->upload($picture);
				}
				//creating views per group.
				$viewers = $createForm->get('groups')->getData();
				foreach($viewers as $viewer) {
					$announceViewer = new AnnouncementViewers();
					$announceViewer->setAnnouncement($announcement);
					$announceViewer->setChildGroup($viewer);
					$em->persist($announceViewer);
				}
				$em->persist($announcement);
				$em->flush();
			}
		}
		
		$blocks = $em->getRepository(Announcement::class)->allOrdered();
		return $this->render('admin/block.html.twig', ['blocks' => $blocks, 'form' => $createForm->createView(), 'mode' => $mode]);
	}
	
	public function block_edit($block_id) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		
		$repository = $this->getDoctrine()->getRepository(Announcement::class);
		$block = $repository->find($block_id);
		if(!$block) {
			throw $this->createNotFoundException('No announcement found with id ' . $block_id);
		}
		
		return $this->redirectToRoute("admin_block", array('message' => "Group" . $block->getTitle() . " removed."));
		
	}
	
	public function block_delete($block_id) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		
		$repository = $this->getDoctrine()->getRepository(Announcement::class);
		$block = $repository->find($block_id);
		if(!$block) {
			throw $this->createNotFoundException('No announcement found with id ' . $block_id);
		}
		$em->remove($block);
		$em->flush();
		
		return $this->redirectToRoute("admin_block", array('message' => "Group" . $block->getTitle() . " removed."));
		
	}
	
	public function group(Request $request, $message) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		
		//creating the new group.
		$group = new Group();
		$form = $this->createForm(GroupForm::class, $group);
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			$group = $form->getData();
			$em->persist($group);
			$em->flush();
		}
		
		$groups = $em->getRepository(Group::class)->findAll();
		
		return $this->render('admin/group.html.twig', ['groups' => $groups, 'form' => $form->createView()]);
	}
	
	public function group_delete($group_id) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$em = $this->getDoctrine()->getManager();
		
		$repository = $this->getDoctrine()->getRepository(Group::class);
		$group = $repository->find($group_id);
		if(!$group) {
			throw $this->createNotFoundException('No product found with id ' . $group_id);
		}
		if(count($group->getChildren()) > 0) {
			return $this->render('admin/group_delete.html.twig', ['message' => "Group" . $group->getName() . " can't be removed because it contains children."]);
		}
		$em->remove($group);
		$em->flush();
		$this->addFlash("success", "Group deleted");
		return $this->redirectToRoute("admin_group", array('message' => "Group" . $group->getName() . " can't be removed because it contains children."));
		
	}
	
	public function enrollments(Request $request, \Swift_Mailer $mailer) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		$groups = $em->getRepository(Group::class)->findAll();
		
		$enrollment = new Enrollment();
		$form = $this->createForm(EnrollmentForm::class, $enrollment, array(
			'data_class' => null,
			'data' => $groups
		));
		if($request->isMethod('POST')) {
			$form->handleRequest($request);
			
			if($form->isValid()) {
				$enrollment->setGroup($em->getRepository(Group::class)->find($form->get('group')->getData()));
				$enrollment->setEmail($form->get('email')->getData());
				try {
					$enrollment->setCreationDate(new \DateTime("now"));
				} catch(\Exception $e) {
					$e->getMessage();
				}
				$enrollment->setExpired(false);
				$enrollment->generate_enrollment_hash();
				$this->send_registration_email($mailer, $enrollment->getEmail(), $enrollment->getEnrollmentHash());
				$em->persist($enrollment);
				$em->flush();
				$this->addFlash("success", "Created enrollment");
			}
		}
		
		$enrollments = $em->getRepository(Enrollment::class)->getAllOrderedByDateDesc();
		
		return $this->render('admin/enrollments.html.twig', ['enrollments' => $enrollments, 'form' => $form->createView()]);
	}
	
	private function send_registration_email(\Swift_Mailer $mailer, $email, $enrl_hash) {
		$registration_link = 'localhost:8000/parent-register/'.$enrl_hash;
		$message = (new \Swift_Message('Registration Email'))
			->setFrom('dorin.artem.test@gmail.com')
			->setTo($email)
			->setBody($this->renderView('emails/invitation.html.twig', ['link' => $registration_link]), 'text/html')
			->addPart("Your registration link for Happy Childhood: " . $registration_link, 'text/plain');
		$mailer->send($message);
	}
	
	public function users(Request $request, UserPasswordEncoderInterface $passwordEncoder, LoggerInterface $logger) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$em = $this->getDoctrine()->getManager();
		
		$user = new User();
		$form = $this->createForm(UserCreate::class, $user);
		
		//will only happen on POST.
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			
			//password encoding.
			$password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($password);
			$user->setDisabled(false);
			
			//saving the user.
			$em->persist($user);
			$em->persist($user->getInbox());
			$em->flush();
		}
		
		$users = $em->getRepository(User::class)->findAll();
		
		return $this->render('admin/users.html.twig', ['form' => $form->createView(), 'users' => $users]);
	}
	
	/**
	 * @param $name
	 * @return BinaryFileResponse
	 */
	public function showImage($name)
	{
		$response = new BinaryFileResponse(Announcement::getUploadRootDir().'/'.$name);
		$response->trustXSendfileTypeHeader();
		$response->setContentDisposition(
			ResponseHeaderBag::DISPOSITION_INLINE,
			$name,
			iconv('UTF-8', 'ASCII//TRANSLIT', $name)
		);
		
		return $response;
	}
}