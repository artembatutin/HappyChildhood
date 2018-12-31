<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParentFamilyLinkRepository")
 */
class ParentFamilyLink {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Family", inversedBy="parentFamilyLinks")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $family_id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="parentFamilyLinks")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $parent_id;
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getFamilyId(): ?Family {
		return $this->family_id;
	}
	
	public function setFamilyId(?Family $family_id): self {
		$this->family_id = $family_id;
		
		return $this;
	}
	
	public function getParentId(): ?User {
		return $this->parent_id;
	}
	
	public function setParentId(?User $parent_id): self {
		$this->parent_id = $parent_id;
		
		return $this;
	}
}
