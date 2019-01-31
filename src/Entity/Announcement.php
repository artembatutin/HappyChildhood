<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a block/announcement in the system made by an Admin.
 * @ORM\Entity(repositoryClass="App\Repository\AnnouncementRepository")
 */
class Announcement {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="announcements")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $user;
	
	/**
	 * @ORM\Column(type="string", length=64)
	 */
	private $title;
	
	/**
	 * @ORM\Column(type="string", length=1000)
	 */
	private $message;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	private $creation_date;
	
	/**
	 * @ORM\Column(type="array")
	 */
	private $files;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $public;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $hidden;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $commenting;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\AnnouncementViewers", mappedBy="announcement", orphanRemoval=true)
	 */
	private $announcementViewers;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="announcement", orphanRemoval=true)
	 */
	private $comments;
	
	/**
	 * Announcement constructor.
	 */
	public function __construct() {
		$this->announcementViewers = new ArrayCollection();
		$this->comments = new ArrayCollection();
	}
	
	/**
	 * @return int|null
	 */
	public function getId(): ?int {
		return $this->id;
	}
	
	/**
	 * @return User|null
	 */
	public function getUser(): ?User {
		return $this->user;
	}
	
	/**
	 * @param User|null $user
	 * @return Announcement
	 */
	public function setUser(?User $user): self {
		$this->user = $user;
		
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getTitle(): ?string {
		return $this->title;
	}
	
	/**
	 * @param string $title
	 * @return Announcement
	 */
	public function setTitle(string $title): self {
		$this->title = $title;
		return $this;
	}
	
	/**
	 * @return string|null
	 */
	public function getMessage(): ?string {
		return $this->message;
	}
	
	/**
	 * @param string $message
	 * @return Announcement
	 */
	public function setMessage(string $message): self {
		$this->message = $message;
		return $this;
	}
	
	/**
	 * @return \DateTimeInterface|null
	 */
	public function getCreationDate(): ?\DateTimeInterface {
		return $this->creation_date;
	}
	
	/**
	 * @param \DateTimeInterface $creation_date
	 * @return Announcement
	 */
	public function setCreationDate(\DateTimeInterface $creation_date): self {
		$this->creation_date = $creation_date;
		
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getFiles() {
		return $this->files;
	}
	
	/**
	 * @param $files
	 * @return Announcement
	 */
	public function setContent($files): self {
		$this->files = $files;
		
		return $this;
	}
	
	/**
	 * @return bool|null
	 */
	public function isPublic(): ?bool {
		return $this->public;
	}
	
	/**
	 * @param bool $public
	 * @return Announcement
	 */
	public function setPublic(bool $public): self {
		$this->public = $public;
		return $this;
	}
	
	/**
	 * @return bool|null
	 */
	public function getHidden(): ?bool {
		return $this->hidden;
	}
	
	/**
	 * @param bool $hidden
	 * @return Announcement
	 */
	public function setHidden(bool $hidden): self {
		$this->hidden = $hidden;
		return $this;
	}
	
	/**
	 * @return bool|null
	 */
	public function isCommenting(): ?bool {
		return $this->hidden;
	}
	
	/**
	 * @param bool $commenting
	 * @return Announcement
	 */
	public function setCommenting(bool $commenting): self {
		$this->commenting = $commenting;
		return $this;
	}
	
	/**
	 * @return Collection|AnnouncementViewers[]
	 */
	public function getAnnouncementViewers(): Collection {
		return $this->announcementViewers;
	}
	
	/**
	 * @param AnnouncementViewers $announcementViewer
	 * @return Announcement
	 */
	public function addAnnouncementViewer(AnnouncementViewers $announcementViewer): self {
		if(!$this->announcementViewers->contains($announcementViewer)) {
			$this->announcementViewers[] = $announcementViewer;
			$announcementViewer->setAnnouncement($this);
		}
		
		return $this;
	}
	
	/**
	 * @param AnnouncementViewers $announcementViewer
	 * @return Announcement
	 */
	public function removeAnnouncementViewer(AnnouncementViewers $announcementViewer): self {
		if($this->announcementViewers->contains($announcementViewer)) {
			$this->announcementViewers->removeElement($announcementViewer);
			// set the owning side to null (unless already changed)
			if($announcementViewer->getAnnouncement() === $this) {
				$announcementViewer->setAnnouncement(null);
			}
		}
		
		return $this;
	}
	
	/**
	 * @return Collection|Comment[]
	 */
	public function getComments(): Collection {
		return $this->comments;
	}
	
	/**
	 * @param Comment $comment
	 * @return Announcement
	 */
	public function addComment(Comment $comment): self {
		if(!$this->comments->contains($comment)) {
			$this->comments[] = $comment;
			$comment->setAnnouncement($this);
		}
		return $this;
	}
	
	/**
	 * @param Comment $comment
	 * @return Announcement
	 */
	public function removeComment(Comment $comment): self {
		if($this->comments->contains($comment)) {
			$this->comments->removeElement($comment);
			// set the owning side to null (unless already changed)
			if($comment->getAnnouncement() === $this) {
				$comment->setAnnouncement(null);
			}
		}
		
		return $this;
	}
}
