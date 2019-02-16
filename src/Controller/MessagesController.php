<?php
/**
 * Created by PhpStorm.
 * User: dorin
 * Date: 15-Jan-2019
 * Time: 9:11 PM
 */

namespace App\Controller;

use App\Entity\Inbox;
use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Entity\MessageReceiver;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MessagesController extends AbstractController {
	
	/**
	 * @return Response
	 */
	public function inboxNum() {
		$user = $this->getUser();
		$inbox = $user->getInbox();
		$mrRepo = $this->getDoctrine()->getRepository('App:MessageReceiver');
		$qb = $mrRepo->createQueryBuilder('mr');
		$qb->join('mr.message', 'm')
		   ->where('mr.receiver_inbox = ?1')
		   ->andWhere('mr.read_flag = 0')
		   ->select('count(m.id)')
		   ->setParameter(1, $inbox->getId());
		$count = $qb->getQuery()->getSingleScalarResult();
		
		return $this->render('messages/unread_icon.html.twig', array('unread_ms_count' => $count));
	}
	
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
	 * @param $message_id
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function display_message($message_id) {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$user = $this->getUser();
		$inbox = $user->getInbox();
		$em = $this->getDoctrine()->getManager();
		$message = $em->getRepository(Message::class)->find($message_id);
		$allow = $inbox->getId() == $message->getSender_Inbox()->getId();
		$message_receivers = $message->getMessageReceivers();
		foreach($message_receivers as $mr) {
			if($mr->getReceiver_Inbox()->getId() == $inbox->getId()) {
				if($mr->getRead_Flag() == false) {
					$mr->setReadFlag(true);
					$em->persist($mr);
					$em->flush();
				}
				$allow = true;
				break;
			}
		}
		$attachments = $message->getAttachments();
		if($allow) {
			return $this->render('messages/display_message.html.twig', array('user' => $user, 'inbox' => $inbox, 'message' => $message, 'msr' => $message_receivers, 'attachments' => $attachments));
		} else
			return $this->redirectToRoute('index');
	}
	
	/**
	 * @param $message_id
	 * @param $attachment_id
	 * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function download_attachment($message_id, $attachment_id) {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$user = $this->getUser();
		$inbox = $user->getInbox();
		$em = $this->getDoctrine()->getManager();
		$message = $em->getRepository(Message::class)->find($message_id);
		$attachment = $em->getRepository(MessageAttachment::class)->find($attachment_id);
		if($message->getId() != $attachment->getMessage()->getId()) {
			return $this->redirectToRoute('index');
		}
		$message_receivers = $message->getMessageReceivers();
		$allow = $inbox->getId() == $message->getSender_Inbox()->getId();
		foreach($message_receivers as $mr) {
			if($mr->getReceiver_Inbox()->getId() == $inbox->getId()) {
				$allow = true;
				break;
			}
		}
		if(!$allow) {
			return $this->redirectToRoute('index');
		}
		$path = $attachment->getAbsolutePath();
		$file = new File($path);
		return $this->file($file);
	}
	
	/**
	 * @param Request $request
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 */
	public function new_message(Request $request) {
		if(!$this->isGranted("IS_AUTHENTICATED_FULLY")) {
			return $this->redirectToRoute('index');
		}
		$isStaff = $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MOD');
		$user = $this->getUser();
		$inbox = $user->getInbox();
		$em = $this->getDoctrine()->getManager();
		
		$message = new Message();
		$message->setSenderInbox($inbox);
		try {
			$message->setDateSent(new \DateTime("now"));
		} catch(\Exception $e) {
		}
		$createForm = $isStaff ?
			//admin message sending option allowing to send to everyone and select specific users.
			$this->createFormBuilder()
			->add('everyone', CheckboxType::class, array('required' => false,'mapped' => false, 'label' => 'Send this message to everyone.'))
			->add('user', ChoiceType::class, array('choices' => $em->getRepository(User::class)->findAll(), 'label' => 'All Users', 'choice_label' => function($user, $key, $value) {
				return $user->getFirstName() . " " . $user->getLastName();
			}, 'choice_value' => function(User $user = null) {
				return $user ? $user->getId() : '';
			}, 'mapped' => false, 'multiple' => true, 'required' => false))
			->add('title', TextType::class)
			->add('attachments', FileType::class, ['multiple' => true, 'required' => false])
			->add('message', TextareaType::class, array('empty_data' => 'Your message here...',))
			->getForm() :
			//regular parent/user sending a message only to staff.
			$this->createFormBuilder()
				->add('user', ChoiceType::class, array('choices' => $em->getRepository(User::class)->findByRole('ROLE_ADMIN'), 'label' => 'Administrators', 'choice_label' => function($user, $key, $value) {
					return $user->getFirstName() . " " . $user->getLastName();
				}, 'choice_value' => function(User $user = null) {
					return $user ? $user->getId() : '';
				}, 'mapped' => false, 'multiple' => true, 'required' => false))
			->add('title', TextType::class)
			->add('attachments', FileType::class, ['multiple' => true, 'required' => false])
			->add('message', TextareaType::class, array('empty_data' => 'Your message here...',))
			->getForm();;
		
		if($request->isMethod('POST')) {
			$createForm->handleRequest($request);
			
			if($createForm->isValid()) {
				$message->setTitle($createForm->get('title')->getData());
				$message->setMessageFile($createForm->get('message')->getData());
				$em->persist($message);
				$attachments = $createForm->get('attachments')->getData();
				foreach($attachments as $attachment) {
					$atchm = new MessageAttachment();
					$atchm->setFileName($attachment->getClientOriginalName());
					$atchm->setData($attachment);
					$atchm->setMessage($message);
					$atchm->upload();
					$em->persist($atchm);
				}
				if($createForm->has("everyone") && $createForm->get('everyone')->getData() && $isStaff) {
					//sending to all (staff only).
					$inboxes = $em->getRepository(Inbox::class)->findAll();
					foreach($inboxes as $i) {
						$messageReceiver = new MessageReceiver();
						$messageReceiver->setMessage($message);
						$messageReceiver->setReceiverInbox($i);
						$messageReceiver->setReadFlag(false);
						$em->persist($messageReceiver);
					}
				} else {
					//sending to specific users (staff only).
					$receivers = $createForm->get('user')->getData();
					foreach($receivers as $u) {
						$i = $em->getRepository(Inbox::class)->findOneBy(array('user' => $u));
						$messageReceiver = new MessageReceiver();
						$messageReceiver->setMessage($message);
						$messageReceiver->setReceiverInbox($i);
						$messageReceiver->setReadFlag(false);
						$em->persist($messageReceiver);
					}
				}
				
				$em->flush();
				$this->addFlash('success', 'Message successfully sent.');
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
		return $this->render('messages/admin_group_edit.html.twig');
	}
	
	/**
	 * @param $receiver_inbox_id
	 * @return mixed
	 */
	public function getInboxOrdered($receiver_inbox_id) {
		return $this->getDoctrine()->getRepository(MessageReceiver::class)->getOrdered($receiver_inbox_id);
	}
}