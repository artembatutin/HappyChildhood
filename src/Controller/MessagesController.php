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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MessagesController extends AbstractController {
	
	public function inbox() {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$user = $this->getUser();
		$inbox = $user->getInbox();
		$messages_in = $inbox->getReceivedMessages();
		
		$em = $this->getDoctrine()->getManager();
		$users = $em->getRepository(User::class)->findAll();
		
		$message = new Message();
		$message->setSenderInbox($inbox);
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
		
		return $this->render('messages/messages.html.twig', array('user' => $user, 'inbox' => $inbox, 'messages_in' => $messages_in, 'createForm' => $createForm->createView()));
	}
	
}