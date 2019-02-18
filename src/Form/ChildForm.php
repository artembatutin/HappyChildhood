<?php
/**
 * Created by PhpStorm.
 * User: dorin
 * Date: 14-Feb-2019
 * Time: 12:27 AM
 */

namespace App\Form;


use App\Entity\Child;
use App\Entity\Family;
use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChildForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$current_year = (int) date('Y');
		$last_year_in_list = $current_year-7;
		$builder    ->add('first_name', TextType::class)
					->add('last_name', TextType::class)
					->add('group', ChoiceType::class, [
						'choices' => $options['data']['group'],
						'choice_label' => function (Group $group) {
							return $group->getName();
						},
						'placeholder' => false,
						'disabled' => true
					])
					->add('birth_date', BirthdayType::class, [
						'years' => range($current_year, $last_year_in_list),
						'format' => 'yyyy-MM-dd'
					])
					->add('allergies', TextareaType::class, ['required' => false])
					->add('medication', TextareaType::class, ['required' => false]);
		
		if($options['data']['families'] == null) {
			$builder->add('family', TextType::class, [
				'mapped' => false,
				'help' => 'Alias name for the family. Default value is recommended.',
				'data' => 'Family #1'
			]);
		} else {
			$builder->add('family', ChoiceType::class, [
				'choices' => $options['data']['families'],
				'choice_label' => function(Family $family) {
					return $family->getAlias();
				},
				'placeholder' => false
			])
				->add('new_family', TextType::class, [
					'mapped' => false,
					'help' => 'If child is part of a different family enter a new name for the new family here.',
					'required' => false
				]);
		}
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array('data_class' => Child::class));
	}
}