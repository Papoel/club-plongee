<?php

namespace App\Form;

use App\Entity\Calendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'title', type: TextType::class, options: [
                'label' => 'Titre',
            ])

            ->add(child: 'start', type: DateTimeType::class, options: [
                'label' => 'Début',
                'widget' => 'single_text',
            ])

            ->add(child: 'end', type: DateTimeType::class, options: [
                'label' => 'Fin',
                'widget' => 'single_text',
            ])

            ->add(child: 'description', type: TextareaType::class, options: [
                'label' => 'Description',
            ])

            ->add(child: 'all_day', type: CheckboxType::class, options: [
                'label' => 'Journée entière',
                'label_attr' => ['class' => 'checkbox-inline checkbox-switch'],
                'required' => false,
            ])

            ->add(child: 'background_color', type: ChoiceType::class, options: [
            'label' => 'Couleur de fond',
            'choices' => [
                'Theme' => '#1f96a5',
                'Bleu' => '#007bff',
                'Vert' => '#28a745',
                'Rouge' => '#dc3545',
                'Jaune' => '#ffc107',
                'Turquoise' => '#17a2b8',
                'Rose' => '#e83e8c',
                'Violet' => '#6f42c1',
                'Gris' => '#6c757d',
                'Noir' => '#363636',
                'Blanc' => '#ffffff',
            ],
            'required' => false,
            'placeholder' => 'Choisir une couleur de fond',
            ])

            ->add(child: 'border_color', type: ChoiceType::class, options: [
                'label' => 'Couleur de bordure',
                'choices' => [
                    'Theme' => '#1f96a5',
                    'Bleu' => '#007bff',
                    'Vert' => '#28a745',
                    'Rouge' => '#dc3545',
                    'Jaune' => '#ffc107',
                    'Turquoise' => '#17a2b8',
                    'Rose' => '#e83e8c',
                    'Violet' => '#6f42c1',
                    'Gris' => '#6c757d',
                    'Noir' => '#363636',
                    'Blanc' => '#ffffff',
                ],
                'required' => false,
                'placeholder' => 'Choisir une couleur de bordure',
            ])

            ->add(child: 'text_color', type: ChoiceType::class, options: [
                'label' => 'Couleur du texte',
                'choices' => [
                    'Theme' => '#1f96a5',
                    'Bleu' => '#007bff',
                    'Vert' => '#28a745',
                    'Rouge' => '#dc3545',
                    'Jaune' => '#ffc107',
                    'Turquoise' => '#17a2b8',
                    'Rose' => '#e83e8c',
                    'Violet' => '#6f42c1',
                    'Gris' => '#6c757d',
                    'Noir' => '#363636',
                    'Blanc' => '#ffffff',
                ],
                'required' => false,
                'placeholder' => 'Choisir une couleur de texte',
            ])

            ->add(child: 'recurrent', type: CheckboxType::class, options: [
                'label' => 'Événement récurrent ?',
                'label_attr' => ['class' => 'checkbox-inline checkbox-switch'],
                'required' => false,
            ])

            ->add(child: 'daysOfWeek', type: ChoiceType::class, options: [
                'label' => 'Jours de la semaine',
                'choices' => [
                    'Lundi' => 1,
                    'Mardi' => 2,
                    'Mercredi' => 3,
                    'Jeudi' => 4,
                    'Vendredi' => 5,
                    'Samedi' => 6,
                    'Dimanche' => 0,
                ],
                // Add margin after each value for better readability
                'choice_attr' => fn ($choice, $key, $value) => ['class' => 'me-1 ms-2'],
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])

            ->add(child: 'startTime', type: DateTimeType::class, options: [
                // Display only hours and minutes
                'format' => 'HH:mm',
                'label' => 'Heure de début',
                'required' => false,
                'widget' => 'single_text',
                'html5' => false,
            ])

            ->add(child: 'endTime', type: DateTimeType::class, options: [
                'format' => 'HH:mm',
                'label' => 'Heure de fin',
                'required' => false,
                'widget' => 'single_text',
                'html5' => false,
            ])

            ->add(child: 'startRecur', type: DateType::class, options: [
                'label' => 'Début de récurrence',
                'required' => false,
                'widget' => 'single_text',
            ])

            ->add(child: 'endRecur', type: DateType::class, options: [
                'label' => 'Fin de récurrence',
                'required' => false,
                'widget' => 'single_text',
            ])

            ->add(child: 'duration', type: TextType::class, options: [
                'label' => 'Durée',
                'required' => false,
            ]);

        // Fréquence de récurrence
        $builder->add(child: 'frequency', type: ChoiceType::class, options: [
            'label' => 'Fréquence de récurrence',
            'choices' => [
                'Tous les jours' => 'DAILY',
                'Toutes les semaines' => 'WEEKLY',
                'Tous les mois' => 'MONTHLY',
                'Tous les ans' => 'YEARLY',
            ],
            'required' => false,
            'placeholder' => 'Choisir une fréquence de récurrence',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
