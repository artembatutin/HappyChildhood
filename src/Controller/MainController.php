<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\AnnouncementViewers;
use App\Entity\Group;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController {
	public function index(LoggerInterface $logger) {
		$blocks = null;
		$em = $this->getDoctrine()->getManager();
		if($this->isGranted("IS_AUTHENTICATED_FULLY")) {
			$user = $this->getUser();
			if($this->isGranted("ROLE_MOD")) {
				$blocks = $em->getRepository(Announcement::class)->findAll();
			} else {
				$blocks = $em->getRepository(Announcement::class)->findForUser($user);
			}
		} else {
			$blocks = $em->getRepository(Announcement::class)->findBy(['public' => true, 'hidden' => false]);
		}
		
		return $this->render('index.html.twig', ['blocks' => $blocks]);
	}
}