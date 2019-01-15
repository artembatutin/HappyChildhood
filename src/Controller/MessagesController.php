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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class MessagesController extends AbstractController  {
	
	public function inbox() {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$user = $this->getUser();
		$inbox = $user->getInbox();
		$messages_in = $inbox->getReceivedMessages();
		
		return $this->render('messages/messages.html.twig', array('user' => $user, 'inbox' => $inbox, 'messages_in' => $messages_in));
	}
	
}