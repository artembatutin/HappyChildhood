<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\Group;
use App\Form\GroupForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController {
	
	public function content(Request $request) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		$em = $this->getDoctrine()->getManager();
		
		$blocks = $em->getRepository(Announcement::class)->findAll();
		
		return $this->render('admin/block.html.twig', ['blocks' => $blocks]);
	}
	
	public function group(Request $request) {
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
	
	public function group_delete($group) {
		$this->denyAccessUnlessGranted('ROLE_ADMIN');
		
		if(count($group->getChildren()) > 0) {
			return $this->render('admin/group_delete.html.twig', ['message' => "Group" . $group->getName() . " can't be removed because it contains children."]);
		}
		
		$em = $this->getDoctrine()->getManager();
		$em->remove($group);
		$em->flush();
		
		return $this->render('admin/group_delete.html.twig', ['message' => "Group" . $group->getName() . " Removed."]);
		
	}
}