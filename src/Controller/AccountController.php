<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Security\LoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController {
	
	/**
	 * @Route("/login", name="app_login")
	 */
	public function login(AuthenticationUtils $authenticationUtils): Response {
		if($this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		// last login error.
		$error = $authenticationUtils->getLastAuthenticationError();
		// last entered username.
		$lastUsername = $authenticationUtils->getLastUsername();
		
		return $this->render('account/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
	}
	
	/**
	 * @Route("/register", name="user_registration")
	 */
	public function register(Request $request, LoginAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler, UserPasswordEncoderInterface $passwordEncoder) {
		
		$user = new User();
		$form = $this->createForm(UserType::class, $user);
		
		//will only happen on POST.
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			
			//password encoding.
			$password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
			$user->setPassword($password);
			$user->setDisabled(false);
			
			//saving the user.
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();
			
			
			return $guardHandler->authenticateUserAndHandleSuccess(
				$user,
				$request,
				$authenticator,
				'main'
			);
		}
		
		return $this->render('account/register.html.twig', array('form' => $form->createView()));
	}
	
	public function profile() {
		return $this->render('account/profile.html.twig');
	}
}
