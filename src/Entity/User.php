<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Represents a User in the system.
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 * fields={"email"},
 * errorPath="email",
 * message="It appears you have already registered with this email."
 *)
 */
class User implements UserInterface {
	/**
	 * @ORM\Id()
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;
	/**
	 * @ORM\Column(type="string", length=180, unique=true)
	 * @Assert\NotBlank
	 * @Assert\Email
	 */
	private $email;
	
	/**
	 * @ORM\Column(type="string", length=64)
	 * @Assert\NotBlank
	 */
	private $firstName;
	
	/**
	 * @ORM\Column(type="string", length=64)
	 * @Assert\NotBlank
	 */
	private $lastName;
	
	/**
	 * @ORM\Column(type="json")
	 */
	private $roles = [];
	
	/**
	 * @ORM\Column(type="string", length=64)
	 */
	private $password;
	
	/**
	 * @Assert\NotBlank
	 * @Assert\Length(max=4096)
	 */
	private $plainPassword;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $disabled;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=6, nullable=true)
     */
    private $postal_code;

    /**
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    private $province;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ParentFamilyLink", mappedBy="parent_id")
     */
    private $parentFamilyLinks;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Announcement", mappedBy="user")
     */
    private $announcements;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Inbox", mappedBy="user", cascade={"persist", "remove"})
     */
    private $inbox;
	
	/**
	 * User constructor.
	 */
	public function __construct() {
                                                                                                         		$this->roles = array('ROLE_USER');
                                                                                                           $this->parentFamilyLinks = new ArrayCollection();
                                                                                                           $this->announcements = new ArrayCollection();
                                                                                                           $this->comments = new ArrayCollection();
                                                                                                         	}
	
	public function getId(): ?int {
                                                                                                         		return $this->id;
                                                                                                         	}
	
	public function getEmail(): ?string {
                                                                                                         		return $this->email;
                                                                                                         	}
	
	public function setEmail(string $email): self {
                                                                                                         		$this->email = $email;
                                                                                                         		return $this;
                                                                                                         	}
	
	public function getFirstName() {
                                                                                                         		return $this->firstName;
                                                                                                         	}
	
	public function setFirstName($firstName): void {
                                                                                                         		$this->firstName = $firstName;
                                                                                                         	}
	
	public function getLastName() {
                                                                                                         		return $this->lastName;
                                                                                                         	}
	
	public function setLastName($lastName): void {
                                                                                                         		$this->lastName = $lastName;
                                                                                                         	}
	
	public function getUsername(): string {
                                                                                                         		return (string)$this->email;
                                                                                                         	}
	
	public function getRoles(): array {
                                                                                                         		return $this->roles;
                                                                                                         	}
	
	public function setRoles(array $roles): self {
                                                                                                         		$this->roles = $roles;
                                                                                                         		return $this;
                                                                                                         	}
	
	public function getPassword() {
                                                                                                         		return $this->password;
                                                                                                         	}
	
	public function getPlainPassword() {
                                                                                                         		return $this->plainPassword;
                                                                                                         	}
	
	public function setPlainPassword($password) {
                                                                                                         		$this->plainPassword = $password;
                                                                                                         	}
	
	public function getSalt() {
                                                                                                         		// The bcrypt and argon2i algorithms don't require a separate salt.
                                                                                                         		// You *may* need a real salt if you choose a different encoder.
                                                                                                         		return null;
                                                                                                         	}
	
	public function eraseCredentials() {
                                                                                                         		// If you store any temporary, sensitive data on the user, clear it here
                                                                                                         		// $this->plainPassword = null;
                                                                                                         	}
	
	public function setPassword(string $password): self {
                                                                                                         		$this->password = $password;
                                                                                                         		return $this;
                                                                                                         	}
	
	public function getDisabled(): ?bool {
                                                                                                         		return $this->disabled;
                                                                                                         	}
	
	public function setDisabled(bool $disabled): self {
                                                                                                         		$this->disabled = $disabled;
                                                                                                         		return $this;
                                                                                                         	}

    public function getConfirmed(): ?bool
    {
        return $this->confirmed;
    }

    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(?string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(?string $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return Collection|ParentFamilyLink[]
     */
    public function getParentFamilyLinks(): Collection
    {
        return $this->parentFamilyLinks;
    }

    public function addParentFamilyLink(ParentFamilyLink $parentFamilyLink): self
    {
        if (!$this->parentFamilyLinks->contains($parentFamilyLink)) {
            $this->parentFamilyLinks[] = $parentFamilyLink;
            $parentFamilyLink->setParentId($this);
        }

        return $this;
    }

    public function removeParentFamilyLink(ParentFamilyLink $parentFamilyLink): self
    {
        if ($this->parentFamilyLinks->contains($parentFamilyLink)) {
            $this->parentFamilyLinks->removeElement($parentFamilyLink);
            // set the owning side to null (unless already changed)
            if ($parentFamilyLink->getParentId() === $this) {
                $parentFamilyLink->setParentId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Announcement[]
     */
    public function getAnnouncements(): Collection
    {
        return $this->announcements;
    }

    public function addAnnouncement(Announcement $announcement): self
    {
        if (!$this->announcements->contains($announcement)) {
            $this->announcements[] = $announcement;
            $announcement->setUser($this);
        }

        return $this;
    }

    public function removeAnnouncement(Announcement $announcement): self
    {
        if ($this->announcements->contains($announcement)) {
            $this->announcements->removeElement($announcement);
            // set the owning side to null (unless already changed)
            if ($announcement->getUser() === $this) {
                $announcement->setUser(null);
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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    public function getInbox(): ?Inbox
    {
        return $this->inbox;
    }

    public function setInbox(?Inbox $inbox): self
    {
        $this->inbox = $inbox;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $inbox === null ? null : $this;
        if ($newUser !== $inbox->getUser()) {
            $inbox->setUser($newUser);
        }

        return $this;
    }
	
}