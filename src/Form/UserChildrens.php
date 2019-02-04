<?php

namespace App\Form;

use App\Entity\Child;
use App\Entity\Family;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserChildrens extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('children', CollectionType::class, array('entry_type' => Child::class, 'entry_options' => array('label' => false), 'allow_add' => true));
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array('data_class' => Family::class,));
	}
}