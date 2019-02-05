<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageReceiverRepository")
 */
class MessageReceiver {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Message", inversedBy="messageReceivers")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $message;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Inbox", inversedBy="received_messages")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $receiver_inbox;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $read_flag;
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getMessage(): ?Message {
		return $this->message;
	}
	
	public function setMessage(?Message $message): self {
		$this->message = $message;
		
		return $this;
	}
	
	public function getReceiver_Inbox(): ?Inbox {
		return $this->receiver_inbox;
	}
	
	public function setReceiverInbox(?Inbox $receiver_inbox): self {
		$this->receiver_inbox = $receiver_inbox;
		
		return $this;
	}
	
	public function getRead_Flag(): ?bool {
		return $this->read_flag;
	}
	
	public function setReadFlag(bool $read_flag): self {
		$this->read_flag = $read_flag;
		
		return $this;
	}
}
