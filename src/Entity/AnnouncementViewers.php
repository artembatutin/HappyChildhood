<?php

namespace App\Entity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnnouncementViewersRepository")
 */
class AnnouncementViewers {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Announcement", inversedBy="announcementViewers")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $announcement;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Group")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $child_group;
	
	/**
	 * @return int|null
	 */
	public function getId(): ?int {
		return $this->id;
	}
	
	/**
	 * @return Announcement|null
	 */
	public function getAnnouncement(): ?Announcement {
		return $this->announcement;
	}
	
	/**
	 * @param Announcement|null $announcement
	 * @return AnnouncementViewers
	 */
	public function setAnnouncement(?Announcement $announcement): self {
		$this->announcement = $announcement;
		return $this;
	}
	
	/**
	 * @return Group|null
	 */
	public function getChildGroup(): ?Group {
		return $this->child_group;
	}
	
	/**
	 * @param Group|null $child_group
	 * @return AnnouncementViewers
	 */
	public function setChildGroup(?Group $child_group): self {
		$this->child_group = $child_group;
		return $this;
	}
}
