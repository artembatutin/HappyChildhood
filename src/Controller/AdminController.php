<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\AnnouncementViewers;
use App\Entity\Child;
use App\Entity\Enrollment;
use App\Entity\Group;
use App\Entity\Inbox;
use App\Entity\User;
use App\Form\ChildEditAdminForm;
use App\Form\EnrollmentForm;
use App\Form\GroupForm;
use App\Form\UserCreate;
use phpDocumentor\Reflection\Types\String_;
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
		
		$repository = $em->getRepository(Announcement::class);
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
	
	public function group(Request $request, $group_id = -1) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		
		//creating the new group.
		$group = new Group();
		
		$mode = "Create";
		//edit mode.
		if($group_id != -1) {
			$group = $em->getRepository(Group::class)->find($group_id);
			$mode = "Edit";
		}
		
		$form = $this->createForm(GroupForm::class, $group);
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			$group = $form->getData();
			$em->persist($group);
			$em->flush();
		}
		
		$groups = $em->getRepository(Group::class)->findAll();
		
		return $this->render('admin/group.html.twig', ['groups' => $groups, 'form' => $form->createView(), 'mode' => $mode]);
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
	
	public function enrollments(Request $request, \Swift_Mailer $mailer, $enrollment_id = -1) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		$groups = $em->getRepository(Group::class)->findAll();
		
		$enrollment = new Enrollment();
		
		$mode = "Create";
		//edit mode.
		if($enrollment_id != -1) {
			$enrollment = $em->getRepository(Enrollment::class)->find($enrollment_id);
			$mode = "Edit";
		}
		
		$form = $this->createForm(EnrollmentForm::class, $enrollment, array(
			'groups' => $groups,
			'mode' => $mode
		));
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			$enrollment = $form->getData();
			//$enrollment->setCanAddChild($form->get('canAddChild')->getData());
			if($enrollment->getCanAddChild()) {
				$enrollment->setGroup($em->getRepository(Group::class)->find($form->get('group')->getData()));
			} else {
				$enrollment->setGroup(null);
			}
			//$enrollment->setEmail($form->get('email')->getData());
			try {
				$enrollment->setCreationDate(new \DateTime("now"));
			} catch(\Exception $e) {
				$e->getMessage();
			}
			if($mode == "Create") {
				$enrollment->setExpired(false);
				$enrollment->generate_enrollment_hash();
				$this->send_registration_email($mailer, $enrollment->getEmail(), $enrollment->getEnrollmentHash(), $request->getHost(), $form->get('firstName')->getData(), $form->get('lastName')->getData());
			}
			$em->persist($enrollment);
			$em->flush();
			$this->addFlash("success", "Created enrollment");
		}
		
		$enrollments = $em->getRepository(Enrollment::class)->getAllOrderedByDateDesc();
		
		return $this->render('admin/enrollments.html.twig', ['enrollments' => $enrollments, 'form' => $form->createView(), 'mode' => $mode]);
	}
	
	public function enrollment_delete($enrollment_id) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		$enrollment = $em->getRepository(Enrollment::class)->find($enrollment_id);
		
		if(!$enrollment) {
			return $this->redirectToRoute('admin_enrollments');
		}
		
		$em->remove($enrollment);
		$em->flush();
		$this->addFlash('success', "Enrollment deleted.");
		return $this->redirectToRoute('admin_enrollments');
	}
	
	public function enrollments_delete_expired() {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		$enrollments = $em->getRepository(Enrollment::class)->findBy(['expired' => true]);
		
		if(!$enrollments) {
			return $this->redirectToRoute('admin_enrollments');
		}
		
		foreach($enrollments as $enrollment) {
			$em->remove($enrollment);
		}
		$em->flush();
		
		return $this->redirectToRoute('admin_enrollments');
	}
	
	/**
	 * @param \Swift_Mailer $mailer
	 * @param $email
	 * @param $enrl_hash
	 */
	private function send_registration_email(\Swift_Mailer $mailer, $email, $enrl_hash, $domain_name, $firstName, $lastName) {
		$registration_link = $domain_name.'/'.$enrl_hash;
		$message = (new \Swift_Message('Registration Email'))
			->setFrom('dorin.artem.test@gmail.com')
			->setTo($email)
			->setBody($this->renderView('emails/invitation.html.twig', ['name' => $firstName . " " . $lastName, 'link' => $registration_link]), 'text/html')
			->addPart("Your registration link for Happy Childhood: " . $registration_link, 'text/plain');
		$mailer->send($message);
	}
	
	/**
	 * @param Request $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param int $user_id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function users(Request $request, UserPasswordEncoderInterface $passwordEncoder, $user_id = -1) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$em = $this->getDoctrine()->getManager();
		
		$user = new User();
		
		$mode = "Create";
		//edit mode.
		if($user_id != -1) {
			$user = $em->getRepository(User::class)->find($user_id);
			$mode = "Edit";
		}
		
		$form = $this->createForm(UserCreate::class, $user, [
			'mode' => $mode
		]);
		
		//will only happen on POST.
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			
			if((!empty($user->getPlainPassword()) && $mode == "Edit") || $mode == "Create") {
				//password encoding.
				$password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
				$user->setPassword($password);
			}
			
			//saving the user.
			$em->persist($user);
			$em->persist($user->getInbox());
			$em->flush();
		}
		
		$users = $em->getRepository(User::class)->findAll();
		
		$current_user = $this->getUser();
		
		return $this->render('admin/users.html.twig', ['form' => $form->createView(), 'users' => $users, 'current_user' => $current_user, 'mode' => $mode]);
	}
	
	public function user_disable($user_id) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$em = $this->getDoctrine()->getManager();
		
		$user = $em->getRepository(User::class)->find($user_id);
		
		if(!$user) {
			throw $this->createNotFoundException('No user found with id ' . $user_id);
		}
		$user->setDisabled(true);
		$em->persist($user);
		$em->flush();
		return $this->redirectToRoute("admin_users");
	}
	
	public function user_enable($user_id) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$em = $this->getDoctrine()->getManager();
		
		$user = $em->getRepository(User::class)->find($user_id);
		
		if(!$user) {
			throw $this->createNotFoundException('No user found with id ' . $user_id);
		}
		$user->setDisabled(false);
		$em->persist($user);
		$em->flush();
		return $this->redirectToRoute("admin_users");
	}
	
	public function user_delete($user_id) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$em = $this->getDoctrine()->getManager();
		
		$user = $em->getRepository(User::class)->find($user_id);
		if(!$user) {
			throw $this->createNotFoundException('No user found with id ' . $user_id);
		}
		$em->remove($user);
		$em->flush();
		return $this->redirectToRoute("admin_users");
	}
	
	/**
	 * @param bool $allergies_flag
	 * @param int $child_id
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function children(Request $request, $allergies_flag = false, $child_id = -1) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		$em = $this->getDoctrine()->getManager();
		
		$mode = "Create";
		$form = null;
		//edit mode.
		if($child_id != -1) {
			$mode = "Edit";
			$child = $em->getRepository(Child::class)->find($child_id);
			$groups = $em->getRepository(Group::class)->findAll();
			$form = $this->createForm(ChildEditAdminForm::class, $child, [
				'groups' => $groups
			]);
			
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid()) {
				$child = $form->getData();
				$em->persist($child);
				$em->flush();
			}
		}
		
		if($allergies_flag) {
			$children = $em->getRepository(Child::class)->getAllWithAllergiesOrMedication();
		} else {
			$children = $em->getRepository(Child::class)->findAll();
		}
		$caretakers = [];
		foreach($children as $index=>$child) {
			array_push($caretakers, $em->getRepository(User::class)->getCaretakersOf($child));
		}
		
		return $this->render('admin/children.html.twig',
			[
				'children' => $children,
				'caretakers' => $caretakers,
				'allergies_flag' => $allergies_flag,
				'mode' => $mode,
				'form' => $form!=null?$form->createView():null
			]);
	}
	
	public function child_delete($child_id) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		$child = $em->getRepository(Child::class)->find($child_id);
		
		if(!$child) {
			return $this->redirectToRoute('admin_children');
		}
		
		$em->remove($child);
		$em->flush();
		
		return $this->redirectToRoute('admin_children');
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