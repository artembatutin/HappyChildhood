<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\Table(name="`group`")
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
     * @ORM\OneToMany(targetEntity="App\Entity\Child", mappedBy="assignedGroup")
     */
    private $children;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Enrollment", mappedBy="group", cascade={"remove"})
	 */
	private $enrollments;
	
	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\AnnouncementViewers", mappedBy="child_group", cascade={"remove"})
	 */
	private $announcementViewers;
	
	public function __construct()
             {
                 $this->children = new ArrayCollection();
                 $this->enrollments = new ArrayCollection();
                 $this->announcementViewers = new ArrayCollection();
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
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Child $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children[] = $child;
            $child->setAssignedGroup($this);
        }

        return $this;
    }

    public function removeChild(Child $child): self
    {
        if ($this->children->contains($child)) {
            $this->children->removeElement($child);
            // set the owning side to null (unless already changed)
            if ($child->getAssignedGroup() === $this) {
                $child->setAssignedGroup(null);
            }
        }

        return $this;
    }
	
	/**
	 * @return Collection|Enrollment[]
	 */
    public function getEnrollments(): Collection
    {
    	return $this->enrollments;
    }
    
    public function addEnrollment(Enrollment $enrollment): self
    {
    	if(!$this->enrollments->contains($enrollment)){
    		$this->enrollments[] = $enrollment;
    		$enrollment->setGroup($this);
	    }
    	
    	return $this;
    }
    
    public function removeEnrollment(Enrollment $enrollment): self
    {
    	if($this->enrollments->contains($enrollment)) {
    		$this->enrollments->removeElement($enrollment);
		    // set the owning side to null (unless already changed)
		    if($enrollment->getGroup() === $this) {
		    	$enrollment->setGroup(null);
		    }
	    }
    	
    	return $this;
    }
	
	/**
	 * @return Collection|AnnouncementViewers[]
	 */
    public function getAnnouncementViewers(): Collection
    {
    	return $this->announcementViewers;
    }
    
    public function addAnnouncementViewer(AnnouncementViewers $announcementViewer):self
    {
	    if(!$this->announcementViewers->contains($announcementViewer)){
		    $this->announcementViewers[] = $announcementViewer;
		    $announcementViewer->setChildGroup($this);
	    }
	
	    return $this;
    }
    
    public function removeAnnouncementViewers(AnnouncementViewers $announcementViewer): self
    {
	    if($this->announcementViewers->contains($announcementViewer)) {
		    $this->announcementViewers->removeElement($announcementViewer);
		    // set the owning side to null (unless already changed)
		    if($announcementViewer->getChildGroup() === $this) {
			    $announcementViewer->setChildGroup(null);
		    }
	    }
	
	    return $this;
    }
}
