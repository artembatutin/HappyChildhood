<?php
/**
 * Created by PhpStorm.
 * User: dorin
 * Date: 19-Feb-2019
 * Time: 11:11 AM
 */

namespace App\Form;


use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageReplyForm extends AbstractType {
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add('attachments', FileType::class, [
				'multiple' => true,
				'required' => false,
				'mapped' => false
			])
			->add('message_file', TextareaType::class, [
				'required' => true
			]);
	}
	
	public function configureOptions(OptionsResolver $resolver) {
		$resolver->setDefaults(array('data_class' => Message::class,));
	}
}