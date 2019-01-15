<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class UserProfile extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('firstName', TextType::class)
			->add('lastName', TextType::class)
			->add('email', EmailType::class)
			->add('address', TextType::class, array('required' => false))
			//->add('prov', 'choice', array(
			//	'choices' => array(
			//		'qc' => 'Quebec',
			//		'on' => 'Onatio'
			//	)
			//))
			->add('postal_code', TextType::class, array('required' => false))
			->add('phone', NumberType::class, array('required' => false));
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array('data_class' => User::class,));
	}
}