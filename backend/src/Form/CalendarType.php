<?php

namespace App\Form;

use App\Entity\Calendar;
use App\EventSubscriber\Form\CalendarFormEventSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $colors = [
            'Theme' => 'primary',
            'Vert' => 'green',
            'Rouge' => 'danger',
            'Jaune' => 'yellow',
            'Turquoise' => 'teal',
            'Gris' => 'secondary',
            'Noir' => 'black',
            'Violet' => 'purple',
            'Rose' => 'pink',
            'Orange' => 'orange',
            'Bleu' => 'blue',
            'Metal' => 'metal',
            'Blanc' => 'white',
        ];
        ksort(array: $colors);

        $builder
            ->add(child: 'title', type: TextType::class, options: [
                'label' => 'Titre',
            ])
            ->add(child: 'start', type: DateTimeType::class, options: [
                'label' => 'Début',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add(child: 'end', type: DateTimeType::class, options: [
                'label' => 'Fin',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add(child: 'description', type: TextareaType::class, options: [
                'label' => 'Description',
                'required' => false,
            ])
            ->add(child: 'all_day', type: CheckboxType::class, options: [
                'label' => 'Oui, toute la journée.',
                'label_attr' => ['class' => 'checkbox-inline checkbox-switch'],
                'required' => false,
            ])
            ->add(child: 'background_color', type: ChoiceType::class, options: [
                'label' => 'Couleur de fond',
                'choices' => $colors,
                'required' => false,
                'placeholder' => 'Choisir une couleur de fond',
            ])
            ->add(child: 'border_color', type: ChoiceType::class, options: [
                'label' => 'Couleur de bordure',
                'choices' => $colors,
                'required' => false,
                'placeholder' => 'Choisir une couleur de bordure',
            ])
            ->add(child: 'text_color', type: ChoiceType::class, options: [
                'label' => 'Couleur du texte',
                'choices' => $colors,
                'required' => false,
                'placeholder' => 'Choisir une couleur de texte',
            ])
            ->add(child: 'recurrent', type: CheckboxType::class, options: [
                'label' => 'Oui, l\'événement est répété.',
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
            ->add(child: 'startTime', type: TimeType::class, options: [
                // Display only hours and minutes
                // 'format' => 'HH:mm',
                'label' => 'De',
                'required' => false,
                'widget' => 'choice',
                'html5' => false,
            ])
            ->add(child: 'endTime', type: TimeType::class, options: [
                // 'format' => 'HH:mm',
                'label' => 'A',
                'required' => false,
                'widget' => 'choice',
                'html5' => false,
            ])
            ->add(child: 'startRecur', type: DateType::class, options: [
                'label' => 'Commence',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add(child: 'endRecur', type: DateType::class, options: [
                'label' => 'Fini',
                'required' => false,
                'widget' => 'single_text',
            ])
            /*->add(child: 'duration', type: TextType::class, options: [
                'label' => 'Durée',
                'required' => false,
            ])*/

            ->addEventSubscriber(new CalendarFormEventSubscriber())
        ;

        // Fréquence de récurrence
        /*$builder->add(child: 'frequency', type: ChoiceType::class, options: [
            'label' => 'Fréquence de récurrence',
            'choices' => [
                'Tous les jours' => 'DAILY',
                'Toutes les semaines' => 'WEEKLY',
                'Tous les mois' => 'MONTHLY',
                'Tous les ans' => 'YEARLY',
            ],
            'required' => false,
            'placeholder' => 'Choisir une fréquence de récurrence',
        ]);*/
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
            'entity_class' => Calendar::class,
        ]);
    }
}
