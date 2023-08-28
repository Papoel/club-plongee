<?php

namespace App\Form;

use App\Entity\Contact;
use App\Form\FormExtension\HoneyPotType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends HoneyPotType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On appelle la méthode buildForm() de la classe parente (HoneyPotType) pour ajouter les champs 'phone' et 'ville'
        // Cela permet d'éviter la soumission du formulaire par un robot.
        parent::buildForm($builder, $options);

        $builder
            ->add(child: 'fullname', type: TextType::class, options: [
                'attr' => [
                    'placeholder' => 'Votre nom complet',
                    'minlength' => 3,
                    'maxlength' => 50,
                ],
                'label' => 'Nom / Prénom',
            ])
            ->add(child: 'email', type: EmailType::class, options: [
                'attr' => [
                    'placeholder' => 'Votre adresse email',
                    'maxlength' => 180,
                ],
                'label' => 'Adresse E-mail',
            ])
            ->add(child: 'subject', type: TextType::class, options: [
                'attr' => [
                    'placeholder' => 'Le sujet de votre message',
                    'maxlength' => 100,
                ],
                'label' => 'Sujet',
                'required' => false,
            ])
            ->add(child: 'message', type: TextareaType::class, options: [
                'attr' => [
                    'placeholder' => 'Votre message',
                    'rows' => 5,
                ],
                'label' => 'Votre message',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
