<?php

namespace App\Entity;

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
	 * User constructor.
	 */
	public function __construct() {
		$this->roles = array('ROLE_USER');
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
	
}