<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EnrollmentRepository")
 * @UniqueEntity("$enrollment_hash")
 */
class Enrollment {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Group")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $group_id;
	
	/**
	 * @ORM\Column(type="string", length=255, unique=true)
	 */
	private $enrollment_hash = null;
	
	/**
	 * @ORM\Column(type="string", length=180)
	 */
	private $email;
	
	/**
	 * @ORM\Column(type="datetime")
	 */
	private $creation_date;
	
	/**
	 * @ORM\Column(type="boolean")
	 */
	private $expired = false;
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getGroup(): ?Group {
		return $this->group_id;
	}
	
	public function setGroup(?Group $group_id): self {
		$this->group_id = $group_id;
		
		return $this;
	}
	
	public function getEnrollmentHash(): ?string {
		return $this->enrollment_hash;
	}
	
	public function setEnrollmentHash(string $enrollment_hash): self {
		$this->enrollment_hash = $enrollment_hash;
		
		return $this;
	}
	
	public function getEmail(): ?string {
		return $this->email;
	}
	
	public function setEmail(string $email): self {
		$this->email = $email;
		
		return $this;
	}
	
	public function getCreationDate(): ?\DateTimeInterface {
		return $this->creation_date;
	}
	
	public function setCreationDate(\DateTimeInterface $creation_date): self {
		$this->creation_date = $creation_date;
		
		return $this;
	}
	
	public function getExpired(): ?bool {
		return $this->expired;
	}
	
	public function setExpired(bool $expired): self {
		$this->expired = $expired;
		
		return $this;
	}
	
	public function generate_enrollment_hash() {
		if($this->enrollment_hash == null) {
			$salt = 'yp2k,.P(&,pAGC!R]%t+gq&mp';
			$plain_text = $this->getEmail().$salt.($this->getCreationDate())->format('Y-m-d H:i:s');
			$cypher_text = hash('sha256', $plain_text);
			$this->setEnrollmentHash($cypher_text);
			return $cypher_text;
		} else {
			return null;
		}
	}
}
