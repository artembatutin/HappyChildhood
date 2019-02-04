<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FamilyRepository")
 */
class Family {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="string", length=50)
	 */
	private $alias;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Child", mappedBy="family_id")
	 */
	private $children;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\ParentFamilyLink", mappedBy="family_id")
	 */
	private $parentFamilyLinks;
	
	public function __construct() {
		$this->children = new ArrayCollection();
		$this->parentFamilyLinks = new ArrayCollection();
	}
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getAlias(): ?string {
		return $this->alias;
	}
	
	public function setAlias(string $alias): self {
		$this->alias = $alias;
		
		return $this;
	}
	
	/**
	 * @return Collection|Child[]
	 */
	public function getChildren(): Collection {
		return $this->children;
	}
	
	public function addChild(Child $child): self {
		if(!$this->children->contains($child)) {
			$this->children[] = $child;
			$child->setFamilyId($this);
		}
		
		return $this;
	}
	
	public function removeChild(Child $child): self {
		if($this->children->contains($child)) {
			$this->children->removeElement($child);
			// set the owning side to null (unless already changed)
			if($child->getFamilyId() === $this) {
				$child->setFamilyId(null);
			}
		}
		
		return $this;
	}
	
	/**
	 * @return Collection|ParentFamilyLink[]
	 */
	public function getParentFamilyLinks(): Collection {
		return $this->parentFamilyLinks;
	}
	
	public function addParentFamilyLink(ParentFamilyLink $parentFamilyLink): self {
		if(!$this->parentFamilyLinks->contains($parentFamilyLink)) {
			$this->parentFamilyLinks[] = $parentFamilyLink;
			$parentFamilyLink->setFamilyId($this);
		}
		
		return $this;
	}
	
	public function removeParentFamilyLink(ParentFamilyLink $parentFamilyLink): self {
		if($this->parentFamilyLinks->contains($parentFamilyLink)) {
			$this->parentFamilyLinks->removeElement($parentFamilyLink);
			// set the owning side to null (unless already changed)
			if($parentFamilyLink->getFamilyId() === $this) {
				$parentFamilyLink->setFamilyId(null);
			}
		}
		
		return $this;
	}
}
