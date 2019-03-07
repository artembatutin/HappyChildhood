<?php

namespace App\Controller;

use App\Entity\Announcement;
use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController {
	public function index() {
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
	
	public function block(Request $request, $block_id) {
		$block = null;
		$addComment = $this->createFormBuilder()->add('message', TextareaType::class, ['mapped' => false, 'required' => true])->getForm();
		$em = $this->getDoctrine()->getManager();
		if($this->isGranted("IS_AUTHENTICATED_FULLY")) {
			$user = $this->getUser();
			if($this->isGranted("ROLE_MOD") || $this->isGranted("ROLE_ADMIN")) {
				$block = $em->getRepository(Announcement::class)->findBy(['id' => $block_id]);
			} else {
				$block = $em->getRepository(Announcement::class)->findUserBlock($user, $block_id);
			}
			if($request->isMethod('POST')) {
				$addComment->handleRequest($request);
				if($addComment->isSubmitted() && $addComment->isValid()) {
					$message = $addComment->get('message')->getData();
					$comment = new Comment();
					$comment->setText($message);
					$comment->setUser($user);
					$comment->setAnnouncement($block[0]);
					$comment->setBlocked(false);
					try {
						$comment->setCreationDate(new \DateTime());
					} catch(\Exception $e) {
						throw $e;
					}
					$block[0]->addComment($comment);
					$em->persist($comment);
					$em->persist($block[0]);
					$em->flush();
					$this->addFlash('success', "Reply added.");
				}
			}
		} else {
			$block = $em->getRepository(Announcement::class)->findBy(['id' => $block_id, 'public' => true, 'hidden' => false]);
		}
		if($block == null) {
			$this->addFlash('danger', "No announcement found.");
			return $this->redirectToRoute('index');
		}
		return $this->render('block.html.twig', ['block' => $block[0], 'form' => $addComment->createView()]);
	}
	
	public function comment_delete($comment_id) {
		$em = $this->getDoctrine()->getManager();
		$comment = $em->getRepository(Comment::class)->find($comment_id);
		if($comment == null) {
			$this->addFlash('danger', "No comment found.");
			return $this->redirectToRoute('index');
		}
		$block = $comment->getAnnouncement();
		if($block == null) {
			$this->addFlash('danger', "No announcement found.");
			return $this->redirectToRoute('index');
		}
		$em->remove($comment);
		$em->flush();
		$this->addFlash('success', "Comment removed.");
		return $this->redirectToRoute('block', ['block_id' => $block->getId()]);
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