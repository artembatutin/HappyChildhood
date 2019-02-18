<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Role\Role;

class UserCreate extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('firstName', TextType::class)
		        ->add('lastName', TextType::class)
		        ->add('email', EmailType::class)
		        ->add('plainPassword', RepeatedType::class, array(
		        	'type' => PasswordType::class,
			        'first_options' => array('label' => 'Password'),
			        'second_options' => array('label' => 'Repeat Password'),
			        'required' => $options['mode']=='Create'?true:false))
				->add('disabled', CheckboxType::class, [
					'data' => false,
					'required' => false
				])
				->add('roles', ChoiceType::class, [
					'choices' => [
						'Parent' => 'ROLE_USER',
						'Admin' => 'ROLE_ADMIN'
					],
					'multiple' => true
				])
				->add('address', TextType::class, [
					'required' => false
				])
				->add('postalCode', TextType::class, [
					'required' => false
				])
				->add('phone', TelType::class, [
					'required' => false,
					'attr' => [
						'pattern' => '[0-9]'
					]
				]);
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array('data_class' => User::class, 'mode' => null));
	}
}