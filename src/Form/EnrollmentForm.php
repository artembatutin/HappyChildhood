<?php
/**
 * Created by PhpStorm.
 * User: dorin
 * Date: 05-Feb-2019
 * Time: 12:50 PM
 */

namespace App\Form;

use App\Entity\Enrollment;
use App\Entity\Group;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnrollmentForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('firstName', TextType::class, ['mapped' => false])
			->add('lastName', TextType::class, ['mapped' => false])
			->add('group', ChoiceType::class, array('choices' => $options['data'], 'choice_label' =>
				function(Group $grp, $key, $value) {
					return $grp->getName();
				})
			)
			->add('email', EmailType::class)
			->add('canAddChild', CheckboxType::class, ['required' => false]);
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array('data_class' => Enrollment::class,));
	}
}