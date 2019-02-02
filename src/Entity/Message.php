<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessageRepository")
 */
class Message {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Inbox", inversedBy="sent_messages")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $sender_inbox;
	
	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $title;
	
	/**
	 * @ORM\Column(type="blob")
	 */
	private $message_file;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	private $date_sent;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\MessageReceiver", mappedBy="message", orphanRemoval=true)
	 */
	private $messageReceivers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\MessageAttachment", mappedBy="message", orphanRemoval=true)
     */
    private $attachments;
	
	public function __construct() {
                        		$this->messageReceivers = new ArrayCollection();
                          $this->attachments = new ArrayCollection();
                        	}
	
	public function getId(): ?int {
                        		return $this->id;
                        	}
	
	public function getSender_Inbox(): ?Inbox {
                        		return $this->sender_inbox;
                        	}
	
	public function setSenderInbox(?Inbox $sender_inbox): self {
                        		$this->sender_inbox = $sender_inbox;
                        		
                        		return $this;
                        	}
	
	public function getTitle(): ?string {
                        		return $this->title;
                        	}
	
	public function setTitle(string $title): self {
                        		$this->title = $title;
                        		
                        		return $this;
                        	}
	
	public function getMessage_File() {
                        		if($this->message_file != '')
                        			return stream_get_contents($this->message_file);
                        		return $this->message_file;
                        	}
	
	public function setMessageFile($message_file): self {
                        		$this->message_file = $message_file;
                        		
                        		return $this;
                        	}
	
	public function getDate_Sent(): ?\DateTimeInterface {
                        		return $this->date_sent;
                        	}
	
	public function setDateSent(\DateTimeInterface $date_sent): self {
                        		$this->date_sent = $date_sent;
                        		
                        		return $this;
                        	}
	
	/**
	 * @return Collection|MessageReceiver[]
	 */
	public function getMessageReceivers(): Collection {
                        		return $this->messageReceivers;
                        	}
	
	public function addMessageReceiver(MessageReceiver $messageReceiver): self {
                        		if(!$this->messageReceivers->contains($messageReceiver)) {
                        			$this->messageReceivers[] = $messageReceiver;
                        			$messageReceiver->setMessage($this);
                        		}
                        		
                        		return $this;
                        	}
	
	public function removeMessageReceiver(MessageReceiver $messageReceiver): self {
                        		if($this->messageReceivers->contains($messageReceiver)) {
                        			$this->messageReceivers->removeElement($messageReceiver);
                        			// set the owning side to null (unless already changed)
                        			if($messageReceiver->getMessage() === $this) {
                        				$messageReceiver->setMessage(null);
                        			}
                        		}
                        		
                        		return $this;
                        	}

    /**
     * @return Collection|MessageAttachment[]
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(MessageAttachment $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
            $attachment->setMessage($this);
        }

        return $this;
    }

    public function removeAttachment(MessageAttachment $attachment): self
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
            // set the owning side to null (unless already changed)
            if ($attachment->getMessage() === $this) {
                $attachment->setMessage(null);
            }
        }

        return $this;
    }
}
