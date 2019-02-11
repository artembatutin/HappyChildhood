<?php

namespace App\Controller;

use App\Entity\Enrollment;
use App\Entity\Family;
use App\Entity\User;
use App\Form\UserChildrens;
use App\Form\UserProfile;
use App\Form\UserRegister;
use App\Security\LoginAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

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
	 * @Route("/register/{enrollment_hash}", name="user_registration")
	 */
	public function register($enrollment_hash, Request $request, LoginAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler, UserPasswordEncoderInterface $passwordEncoder) {
		$em = $this->getDoctrine()->getManager();
		$enrollment = $em->getRepository(Enrollment::class)->findOneBy(['enrollment_hash' => $enrollment_hash]);
		
		if($enrollment == null) {
			return $this->redirectToRoute('index');
		}
		
		if($this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		
		$existing_user = $em->getRepository(User::class)->findOneBy(['email' => $enrollment->getEmail()]);
		if($existing_user != null) {
			return $this->redirectToRoute('login');
		}
		
		$user = new User();
		$user->setEmail($enrollment->getEmail());
		$form = $this->createForm(UserRegister::class, $user);
		
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
			$entityManager->persist($user->getInbox());
			$entityManager->flush();
			
			
			return $guardHandler->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');
		}
		
		return $this->render('account/register.html.twig', array('form' => $form->createView()));
	}
	
	public function profile(Request $request) {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$user = $this->getUser();
		
		//TODO for Dorin: get childrens and allow the creation of childrens in a form.
		/*$em = $this->getDoctrine()->getManager();
		$childrens = array();
		$families = $user->getParentFamilyLinks();
		foreach($families as $family) {
			$familyId = $family->getFamilyId();
			$childs = $em->getRepository('AppBundle:Child')->findBy(['family_id' => $familyId]);
			array_push($childrens, $childs);
		}*/
		
		$family = new Family();
		$childrenForm = $this->createForm(UserChildrens::class, $family);
		
		
		//change profile form
		$profileForm = $this->createForm(UserProfile::class, $user);
		$profileForm->handleRequest($request);
		if($profileForm->isSubmitted() && $profileForm->isValid()) {
			
			$user = $profileForm->getData();
			
			//saving the user.
			$em = $this->getDoctrine()->getManager();
			$em->persist($user);
			$em->flush();
			
		}
		
		return $this->render('account/profile.html.twig', array('profileForm' => $profileForm->createView(), 'childrenForm' => $childrenForm->createView()));
	}
}
