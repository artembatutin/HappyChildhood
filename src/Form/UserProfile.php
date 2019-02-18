<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfile extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('firstName', TextType::class)
		        ->add('lastName', TextType::class)
		        ->add('email', EmailType::class)
				->add('plainPassword', RepeatedType::class, array(
						'type' => PasswordType::class,
						'first_options' => array('label' => 'Password'),
						'second_options' => array('label' => 'Repeat Password'),
						'mapped' => false,
						'required' => false
					)
				)
		        ->add('address', TextType::class, array('required' => false))
		        ->add('postal_code', TextType::class, array('required' => false))
		        ->add('phone', NumberType::class, array('required' => false));
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array('data_class' => User::class,));
	}
}