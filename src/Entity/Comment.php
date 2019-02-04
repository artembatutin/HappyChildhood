<?php

namespace App\Entity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Announcement", inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $announcement;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	private $creation_date;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $blocked;
	
	/**
	 * @ORM\Column(type="text")
	 */
	private $text;
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getAnnouncement(): ?Announcement {
		return $this->announcement;
	}
	
	public function setAnnouncement(?Announcement $announcement): self {
		$this->announcement = $announcement;
		
		return $this;
	}
	
	public function getUser(): ?User {
		return $this->user;
	}
	
	public function setUser(?User $user): self {
		$this->user = $user;
		
		return $this;
	}
	
	public function getCreationDate(): ?\DateTimeInterface {
		return $this->creation_date;
	}
	
	public function setCreationDate(\DateTimeInterface $creation_date): self {
		$this->creation_date = $creation_date;
		
		return $this;
	}
	
	public function getBlocked(): ?bool {
		return $this->blocked;
	}
	
	public function setBlocked(bool $blocked): self {
		$this->blocked = $blocked;
		
		return $this;
	}
	
	public function getText(): ?string {
		return $this->text;
	}
	
	public function setText(string $text): self {
		$this->text = $text;
		
		return $this;
	}
}
