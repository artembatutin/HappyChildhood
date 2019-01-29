<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController {
	public function panel(Request $request) {
		
		$em = $this->getDoctrine()->getManager();
		
		//creating the new group.
		$group = new Group();
		$groupForm = $this->createForm(GroupForm::class, $group);
		$groupForm->handleRequest($request);
		if($groupForm->isSubmitted() && $groupForm->isValid()) {
			$group = $groupForm->getData();
			$em->persist($group);
			$em->flush();
			
		}
		
		$groups = $em->getRepository(Group::class)->findAll();
		
		return $this->render('admin/panel.html.twig', ['groups' => $groups, 'groupForm' => $groupForm->createView()]);
	}
	
	public function delete_group($group) {
		
		if(count($group->getChildren()) > 0) {
			//return
		}
		
		$em = $this->getDoctrine()->getManager();
		$em->remove($group);
		$em->flush();
		
		return $this->render('admin/panel.html.twig', ['groups' => $groups, 'groupForm' => $groupForm->createView()]);
	}
}