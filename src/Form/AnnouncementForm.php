<?php

namespace App\Form;

use App\Entity\Announcement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AnnouncementForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('title', TextType::class)
			->add('message', TextareaType::class, ['label' => 'Content'])
			->add('public', CheckboxType::class, ['label' => 'Announcement public'])
			->add('hidden', CheckboxType::class, ['label' => 'Hide it temporary'])
			->add('commenting', CheckboxType::class, ['label' => 'Allow comments'])
			->add('pictures', FileType::class, ['label' => 'Pictures', 'mapped' => false, 'multiple' => true]);
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array('data_class' => Announcement::class,));
	}
}