<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
	
	public function getChildGroup(): ?Group {
		return $this->child_group;
	}
	
	public function setChildGroup(?Group $child_group): self {
		$this->child_group = $child_group;
		
		return $this;
	}
}
