<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InboxRepository")
 */
class Inbox {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="inbox", cascade={"persist", "remove"})
	 */
	private $user;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="sender_inbox")
	 */
	private $sent_messages;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\MessageReceiver", mappedBy="receiver_inbox")
	 */
	private $received_messages;
	
	public function __construct() {
		$this->sent_messages = new ArrayCollection();
		$this->received_messages = new ArrayCollection();
	}
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getUser(): ?User {
		return $this->user;
	}
	
	public function setUser(?User $user): self {
		$this->user = $user;
		
		return $this;
	}
	
	/**
	 * @return Collection|Message[]
	 */
	public function getSentMessages(): Collection {
		return $this->sent_messages;
	}
	
	public function addSentMessage(Message $sentMessage): self {
		if(!$this->sent_messages->contains($sentMessage)) {
			$this->sent_messages[] = $sentMessage;
			$sentMessage->setSenderInbox($this);
		}
		
		return $this;
	}
	
	public function removeSentMessage(Message $sentMessage): self {
		if($this->sent_messages->contains($sentMessage)) {
			$this->sent_messages->removeElement($sentMessage);
			// set the owning side to null (unless already changed)
			if($sentMessage->getSender_Inbox() === $this) {
				$sentMessage->setSenderInbox(null);
			}
		}
		
		return $this;
	}
	
	/**
	 * @return Collection|MessageReceiver[]
	 */
	public function getReceivedMessages(): Collection {
		return $this->received_messages;
	}
	
	public function addReceivedMessage(MessageReceiver $receivedMessage): self {
		if(!$this->received_messages->contains($receivedMessage)) {
			$this->received_messages[] = $receivedMessage;
			$receivedMessage->setReceiverInbox($this);
		}
		
		return $this;
	}
	
	public function removeReceivedMessage(MessageReceiver $receivedMessage): self {
		if($this->received_messages->contains($receivedMessage)) {
			$this->received_messages->removeElement($receivedMessage);
			// set the owning side to null (unless already changed)
			if($receivedMessage->getReceiver_Inbox() === $this) {
				$receivedMessage->setReceiverInbox(null);
			}
		}
		
		return $this;
	}
}
