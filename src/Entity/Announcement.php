<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnnouncementRepository")
 */
class Announcement
{
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
     * @ORM\Column(type="datetime")
     */
    private $creation_date;

    /**
     * @ORM\Column(type="blob")
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=14)
     */
    private $visibility;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hidden;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AnnouncementViewers", mappedBy="announcement", orphanRemoval=true)
     */
    private $announcementViewers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="announcement", orphanRemoval=true)
     */
    private $comments;

    public function __construct()
    {
        $this->announcementViewers = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creation_date;
    }

    public function setCreationDate(\DateTimeInterface $creation_date): self
    {
        $this->creation_date = $creation_date;

        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): self
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * @return Collection|AnnouncementViewers[]
     */
    public function getAnnouncementViewers(): Collection
    {
        return $this->announcementViewers;
    }

    public function addAnnouncementViewer(AnnouncementViewers $announcementViewer): self
    {
        if (!$this->announcementViewers->contains($announcementViewer)) {
            $this->announcementViewers[] = $announcementViewer;
            $announcementViewer->setAnnouncement($this);
        }

        return $this;
    }

    public function removeAnnouncementViewer(AnnouncementViewers $announcementViewer): self
    {
        if ($this->announcementViewers->contains($announcementViewer)) {
            $this->announcementViewers->removeElement($announcementViewer);
            // set the owning side to null (unless already changed)
            if ($announcementViewer->getAnnouncement() === $this) {
                $announcementViewer->setAnnouncement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAnnouncement($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAnnouncement() === $this) {
                $comment->setAnnouncement(null);
            }
        }

        return $this;
    }
}
