<?php
/**
 * Created by PhpStorm.
 * User: dorin
 * Date: 14-Feb-2019
 * Time: 12:27 AM
 */

namespace App\Form;


use App\Entity\Child;
use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChildEditAdminForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder    ->add('first_name', TextType::class)
					->add('last_name', TextType::class)
					->add('assignedGroup', ChoiceType::class, [
						'choices' => $options['groups'],
						'choice_label' => function (Group $group) {
							return $group->getName();
						},
						'placeholder' => 'Unset Group',
						'required' => false,
						'empty_data' => null
					])
					->add('allergies', TextareaType::class, ['required' => false])
					->add('medication', TextareaType::class, ['required' => false]);
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array('data_class' => Child::class, 'groups' => Group::class));
	}
}