<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\AnnouncementViewers;
use App\Entity\Group;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Email;

class MainController extends AbstractController {
	public function index(LoggerInterface $logger) {
		$blocks = null;
		$em = $this->getDoctrine()->getManager();
		if($this->isGranted("IS_AUTHENTICATED_FULLY")) {
			$user = $this->getUser();
			if($this->isGranted("ROLE_MOD") || $this->isGranted("ROLE_ADMIN")) {
				$blocks = $em->getRepository(Announcement::class)->findAll(['hidden' => false]);
			} else {
				$blocks = $em->getRepository(Announcement::class)->findForUser($user);
			}
		} else {
			$blocks = $em->getRepository(Announcement::class)->findBy(['public' => true, 'hidden' => false]);
		}
		
		return $this->render('index.html.twig', ['blocks' => $blocks]);
	}
	
	public function contact(Request $request, \Swift_Mailer $mailer) {
		//contact us form
		$form = $this->createFormBuilder()
			->add('email', EmailType::class)
			->add('message', TextareaType::class, ['label' => 'Content'])
			->getForm();
		
		//request submitted.
		if($request->isMethod('POST')) {
			$form->handleRequest($request);
			if($form->isSubmitted() && $form->isValid()) {
				$email = $form->get('email')->getData();
				$message = $form->get('message')->getData();
				
				$message = (new \Swift_Message('Contact form submitted.'))
					->setFrom('dorin.artem.test@gmail.com')
					->setTo('dorin.artem.test@gmail.com')
					->setBody($this->renderView('emails/contact.html.twig', ['email' => $email, 'msg' => $message]), 'text/html')
					->addPart("Contact form filled by " . $email . " with message: " . $message, 'text/plain');
				$mailer->send($message);
				$this->addFlash('success', "Thank you for your message.");
			}
		}
		
		return $this->render('contact.html.twig', ['form' => $form->createView()]);
	}
}