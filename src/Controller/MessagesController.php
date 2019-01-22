<?php
/**
 * Created by PhpStorm.
 * User: dorin
 * Date: 15-Jan-2019
 * Time: 9:11 PM
 */

namespace App\Controller;

use App\Entity\User;
use App\Entity\Inbox;
use App\Entity\Message;
use App\Entity\MessageReceiver;
use App\Security\LoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MessagesController extends AbstractController {
	
	/**
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function inbox() {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$user = $this->getUser();
		$inbox = $user->getInbox();
		$messages_in = $this->getInboxOrdered($inbox->getId());
		
		return $this->render('messages/inbox.html.twig', array('user' => $user, 'inbox' => $inbox, 'messages_in' => $messages_in));
	}
	
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function new_message(Request $request) {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$user = $this->getUser();
		$inbox = $user->getInbox();
		$em = $this->getDoctrine()->getManager();
		$users = $em->getRepository(User::class)->findAll();
		
		$message = new Message();
		$message->setSenderInbox($inbox);
		try {
			$message->setDateSent(new \DateTime("now"));
		} catch(\Exception $e) {
		}
		$createForm = $this->createFormBuilder()
			->add('user', ChoiceType::class, array(
				'choices' => $users, 'choice_label' => function($user, $key, $value) {
					return $user->getFirstName() . " " . $user->getLastName();
				},
				'choice_value' => function (User $user = null) {
					return $user ? $user->getId() : '';
				},
				'mapped' => false,
				'multiple' => true))
			->add('title', TextType::class)
			->add('message', TextareaType::class, array('empty_data' => 'Your message here...',))->getForm();
		
		if($request->isMethod('POST')) {
			$createForm->handleRequest($request);
			
			if($createForm->isValid()) {
				$message->setTitle($createForm->get('title')->getData());
				$message->setMessageFile($createForm->get('message')->getData());
				$em->persist($message);
				$em->flush();
				$receivers = $createForm->get('user')->getData();
				foreach($receivers as $u) {
					$i = $em->getRepository(Inbox::class)->findOneBy(array('user' => $u));
					$messageReceiver = new MessageReceiver();
					$messageReceiver->setMessage($message);
					$messageReceiver->setReceiverInbox($i);
					$messageReceiver->setReadFlag(false);
					$em->persist($messageReceiver);
					$em->flush();
				}
				
				$request->getSession()->getFlashBag()->add('notice', 'Message sent.');
				
				return $this->redirectToRoute('messages_inbox');
			}
		}
		
		return $this->render('messages/new_message.html.twig', array('user' => $user, 'inbox' => $inbox, 'createForm' => $createForm->createView()));
	}
	
	/**
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function sent() {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$user = $this->getUser();
		$inbox = $user->getInbox();
		$messages_out = $inbox->getSentMessages();
		
		return $this->render('messages/sent.html.twig', array('user' => $user, 'inbox' => $inbox, 'messages_out' => $messages_out));
	}
	
	/**
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function drafts() {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		return $this->render('messages/drafts.html.twig');
	}
	
	/**
	 * @param $receiver_inbox_id
	 * @return mixed
	 */
	public function getInboxOrdered($receiver_inbox_id) {
		$mrRepo = $this->getDoctrine()->getRepository('App:MessageReceiver');
		$qb = $mrRepo->createQueryBuilder('mr');
		$qb->join('mr.message', 'm')
			->where('mr.receiver_inbox = ?1')
			->orderBy('m.date_sent', 'DESC')
			->setParameter(1, $receiver_inbox_id);
			
		$query = $qb->getQuery();
		$result = $query->getResult();
		return $result;
	}
}