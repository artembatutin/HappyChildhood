<?php

namespace App\Entity;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ChildRepository")
 */
class Child extends AbstractType {
	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;
	
	/**
	 * @ORM\Column(type="string", length=64)
	 */
	private $first_name;
	
	/**
	 * @ORM\Column(type="string", length=64)
	 */
	private $last_name;
	
	/**
	 * @ORM\Column(type="date")
	 */
	private $birth_date;
	
	/**
	 * @ORM\Column(type="text")
	 */
	private $allergies;
	
	/**
	 * @ORM\Column(type="text")
	 */
	private $medications;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Family", inversedBy="children")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $family_id;
	
	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Group", inversedBy="children")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $group_id;
	
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('first_name', TextType::class)
		        ->add('last_name', TextType::class)
		        ->add('birth_date', DateTimeType::class)
		        ->add('allergies', TextType::class)
		        ->add('medications', TextType::class)
		        ->add('save', SubmitType::class);
	}
	
	public function getId(): ?int {
		return $this->id;
	}
	
	public function getFamilyId(): ?int {
		return $this->family_id;
	}
	
	public function setFamilyId(int $family_id): self {
		$this->family_id = $family_id;
		
		return $this;
	}
	
	public function getGroupId(): ?int {
		return $this->group_id;
	}
	
	public function setGroupId(int $group_id): self {
		$this->group_id = $group_id;
		
		return $this;
	}
	
	public function getFirstName(): ?string {
		return $this->first_name;
	}
	
	public function setFirstName(string $first_name): self {
		$this->first_name = $first_name;
		
		return $this;
	}
	
	public function getLastName(): ?string {
		return $this->last_name;
	}
	
	public function setLastName(string $last_name): self {
		$this->last_name = $last_name;
		
		return $this;
	}
	
	public function getBirthDate(): ?\DateTimeInterface {
		return $this->birth_date;
	}
	
	public function setBirthDate(\DateTimeInterface $birth_date): self {
		$this->birth_date = $birth_date;
		
		return $this;
	}
	
	public function getAllergies(): ?string {
		return $this->allergies;
	}
	
	public function setAllergies(string $allergies): self {
		$this->allergies = $allergies;
		
		return $this;
	}
	
	public function getMedications(): ?string {
		return $this->medications;
	}
	
	public function setMedications(string $medications): self {
		$this->medications = $medications;
		
		return $this;
	}
}
