<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 */
class Group {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="string", length=50)
	 */
	private $name;
	
	/**
	 * @ORM\Column(type="smallint", nullable=true)
	 */
	private $max_capacity;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Child", mappedBy="group_id")
	 */
	private $children;
	
	public function __construct() {
		$this->children = new ArrayCollection();
	}
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getName(): ?string {
		return $this->name;
	}
	
	public function setName(string $name): self {
		$this->name = $name;
		
		return $this;
	}
	
	public function getMaxCapacity(): ?int {
		return $this->max_capacity;
	}
	
	public function setMaxCapacity(?int $max_capacity): self {
		$this->max_capacity = $max_capacity;
		
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
			$child->setGroupId($this);
		}
		
		return $this;
	}
	
	public function removeChild(Child $child): self {
		if($this->children->contains($child)) {
			$this->children->removeElement($child);
			// set the owning side to null (unless already changed)
			if($child->getGroupId() === $this) {
				$child->setGroupId(null);
			}
		}
		
		return $this;
	}
}
