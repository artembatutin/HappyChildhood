<?php

namespace App\Form;

use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder->add('name', TextType::class)->add('max_capacity', NumberType::class);
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array('data_class' => Group::class,));
	}
}