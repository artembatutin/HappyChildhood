<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\AnnouncementViewers;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController {
	public function index() {
		$blocks = null;
		if($this->isGranted("IS_AUTHENTICATED_FULLY")) {
			$em = $this->getDoctrine()->getManager();
			$user = $this->getUser();
			$blocks = $em->getRepository(Announcement::class)->findForGroups(3);
			
		}
		return $this->render('index.html.twig', ['blocks' => $blocks]);
	}
}