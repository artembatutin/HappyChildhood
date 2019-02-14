<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
	 * @ORM\OneToMany(targetEntity="App\Entity\ParentFamilyLink", mappedBy="family_id")
	 */
	private $parentFamilyLinks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Child", mappedBy="family")
     */
    private $children;
	
	public function __construct() {
               		$this->parentFamilyLinks = new ArrayCollection();
                 $this->children = new ArrayCollection();
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

    /**
     * @return Collection|Child[]
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Child $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setFamily($this);
        }

        return $this;
    }

    public function removeChild(Child $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getFamily() === $this) {
                $child->setFamily(null);
            }
        }

        return $this;
    }
}
