<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\Group;
use App\Form\AnnouncementForm;
use App\Form\GroupForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController {
	
	public function content(Request $request) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		$groups = $em->getRepository(Group::class)->findAll();
		
		//creating the new announcement.
		$announcement = new Announcement();
		$createForm = $this->createFormBuilder($announcement)
			->add('title', TextType::class)
			->add('message', TextareaType::class, ['label' => 'Content'])
			->add('public', CheckboxType::class, ['label' => 'Announcement public'])
			->add('hidden', CheckboxType::class, ['label' => 'Hide it temporary'])
			->add('commenting', CheckboxType::class, ['label' => 'Allow comments'])
			->add('pictures', FileType::class, ['required' => false, 'label' => 'Pictures', 'mapped' => false, 'multiple' => true])
			->add('groups', ChoiceType::class, array(
				'choices' => $groups, 'choice_label' => function($grp, $key, $value) {
					return $grp->getName();
				},
				'choice_value' => function (Group $grp = null) {
					return $grp ? $grp->getId() : '';
				},
				'mapped' => false,
				'multiple' => true))->getForm();
		
		
		if($request->isMethod('POST')) {
			$createForm->handleRequest($request);
			if($createForm->isSubmitted() && $createForm->isValid()) {
				$announcement = $createForm->getData();
				$announcement->setUser($this->getUser());
				try {
					$announcement->setCreationDate(new \DateTime());
				} catch(\Exception $e) {
					throw $e;
				}
				$em->persist($announcement);
				$em->flush();
			}
		}
		$blocks = $em->getRepository(Announcement::class)->findAll();
		
		return $this->render('admin/block.html.twig', ['blocks' => $blocks, 'form' => $createForm->createView()]);
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
		if (!$group) {
			throw $this->createNotFoundException('No product found for id ' . $group_id);
		}
		if(count($group->getChildren()) > 0) {
			return $this->render('admin/group_delete.html.twig', ['message' => "Group" . $group->getName() . " can't be removed because it contains children."]);
		}
		$em->remove($group);
		$em->flush();
		
		return $this->redirectToRoute("admin_group", array('message' => "Group" . $group->getName() . " can't be removed because it contains children."));
		
	}
}