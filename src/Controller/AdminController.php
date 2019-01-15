<?php

namespace App\Controller;

use App\Entity\Group;
use App\Form\GroupForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController {
	public function panel(Request $request) {
		
		//creating the new group.
		$group = new Group();
		$groupForm = $this->createForm(GroupForm::class, $group);
		$groupForm->handleRequest($request);
		if($groupForm->isSubmitted() && $groupForm->isValid()) {
			$group = $groupForm->getData();
			$em = $this->getDoctrine()->getManager();
			$em->persist($group);
			$em->flush();
			
		}
		
		return $this->render('admin/panel.html.twig', ['groupForm' => $groupForm->createView()]);
	}
}