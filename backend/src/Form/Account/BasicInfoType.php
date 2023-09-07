<?php

declare(strict_types=1);

namespace App\Form\Account;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class BasicInfoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'firstname', type: TextType::class, options: [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(message: 'Le prénom est obligatoire'),
                    new NotNull(message: 'Le prénom est obligatoire'),
                ],
            ])
            ->add(child: 'lastname', type: TextType::class, options: [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(message: 'Le nom est obligatoire'),
                ],
            ])
            ->add(child: 'email', type: TextType::class, options: [
                'label' => 'Email',
                 'constraints' => [
                      new NotBlank(message: 'L\'email est obligatoire'),
                 ],
            ])
            ->add(child: 'phone', type: TelType::class, options: [
                'label' => 'Tél.',
                'required' => false,
            ])
            ->add(child: 'address', type: TextType::class, options: [
                'label' => 'Adresse',
                'constraints' => [
                    new NotBlank(message: 'L\'adresse est obligatoire'),
                ],
            ])
            ->add(child: 'zipCode', type: TextType::class, options: [
                'label' => 'CP',
                 'constraints' => [
                      new NotBlank(message: 'Le code postal est obligatoire'),
                 ],
            ])
            ->add(child: 'city', type: TextType::class, options: [
                'label' => 'Ville',
                 'constraints' => [
                      new NotBlank(message: 'La ville est obligatoire'),
                 ],
            ])
            ->add(child: 'country', type: ChoiceType::class, options: [
                'label' => 'Pays',
                'choices' => [
                    'France' => 'France',
                    'Belgique' => 'Belgique',
                ],
                 'constraints' => [
                      new NotBlank(message: 'Le pays est obligatoire'),
                 ],
            ])

            ->add(child: 'bio', type: TextareaType::class, options: [
                'label' => 'Présentez-vous en quelques mots',
                 'required' => false,
            ])

            ->add(child: 'genre', type: ChoiceType::class, options: [
                'label' => 'Genre',
                'choices' => [
                    'homme' => 'Homme',
                    'femme' => 'Femme',
                    'autre' => 'Autre',
                ],
                'expanded' => true,
                'constraints' => [
                    new NotBlank(message: 'Le genre est obligatoire'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(defaults: [
            'data_class' => User::class,
        ]);
    }
}
