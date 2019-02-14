<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\Enrollment;
use App\Entity\Family;
use App\Entity\Group;
use App\Entity\ParentFamilyLink;
use App\Entity\User;
use App\Form\ChildForm;
use App\Form\UserProfile;
use App\Form\UserRegister;
use App\Security\LoginAuthenticator;
use Psr\Log\LoggerInterface;
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
		
		return $this->render('account/profile.html.twig', array('profileForm' => $profileForm->createView()));
	}
	
	public function family() {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$user = $this->getUser();
		
		$em = $this->getDoctrine()->getManager();
		$enrollments = $em->getRepository(Enrollment::class)->getNonExpired($user->getEmail());
		
		$families = $em->getRepository(ParentFamilyLink::class)->getAllFamiliesOfUser($user->getId());
		
		$canAddChild = false;
		if(sizeof($enrollments) > 0) {
			$canAddChild = true;
		}
		
		return $this->render('account/family.html.twig', array('canAddChild' => $canAddChild, 'families' => $families));
	}
	
	public function add_child(Request $request, LoggerInterface $logger) {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$user = $this->getUser();
		
		$em = $this->getDoctrine()->getManager();
		$enrollments = $em->getRepository(Enrollment::class)->getNonExpired($user->getEmail());
		
		if(sizeof($enrollments) < 1) {
			return $this->redirectToRoute('index');
		}
		
		$group = [$enrollments[0]->getGroup()];
		
		$families = $em->getRepository(ParentFamilyLink::class)->getAllFamiliesOfUser($user->getId());
		if(sizeof($families) < 1) {
			$families = null;
		}
		
		$child = new Child();
		$form = $this->createForm(ChildForm::class, $child, array(
			'data_class' => null,
			'data' => ['group' => $group, 'families' => $families]
		));
		
		$form->handleRequest($request);
		if($form->isSubmitted() && $form->isValid()) {
			$child->setFirstName($form->get('first_name')->getData());
			$child->setLastName($form->get('last_name')->getData());
			$child->setBirthDate($form->get('birth_date')->getData());
			$child->setAssignedGroup($form->get('group')->getData()[0]);
			$child->setAllergies($form->get('allergies')->getData());
			$child->setMedication($form->get('medication')->getData());
			
			if($families == null) {
				$family = new Family();
				$family->setAlias($form->get('family')->getData());
				$em->persist($family);
				$parent_family_link = new ParentFamilyLink();
				$parent_family_link->setFamilyId($family);
				$parent_family_link->setParentId($user);
				$em->persist($parent_family_link);
				$child->setFamily($family);
			} else {
				if(!empty(trim($form->get('new_family')->getData()))) {
					$family = new Family();
					$family->setAlias($form->get('new_family')->getData());
					$em->persist($family);
					$parent_family_link = new ParentFamilyLink();
					$parent_family_link->setFamilyId($family);
					$parent_family_link->setParentId($user);
					$em->persist($parent_family_link);
					$child->setFamily($family);
				} else {
					$family = $form->get('family')->getData();
					$em->persist($family);
					$parent_family_link = new ParentFamilyLink();
					$parent_family_link->setFamilyId($family);
					$parent_family_link->setParentId($user);
					$em->persist($parent_family_link);
					$child->setFamily($family);
				}
			}
			$em->persist($child);
			$enrollments[0]->setExpired(true);
			$em->persist($enrollments[0]);
			$em->flush();
			
			return $this->redirectToRoute('profile');
		}
		
		return $this->render('account/add_child.html.twig', ['form' => $form->createView(), 'families' => $families]);
	}
}
