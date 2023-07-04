<?php

namespace App\Form;

use App\Entity\Calendar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
            ->add(child: 'description', type: TextType::class, options: [
                'label' => 'Description',
            ])
            ->add(child: 'all_day', type: CheckboxType::class, options: [
                'label' => 'Journée entière',
                'label_attr' => ['class' => 'checkbox-inline checkbox-switch'],
                'required' => false,
            ])
            ->add(child: 'background_color', type: ColorType::class, options: [
                'label' => 'Couleur de fond',
            ])
            ->add(child: 'border_color', type: ColorType::class, options: [
                'label' => 'Couleur de bordure',
            ])
            ->add(child: 'text_color', type: ColorType::class, options: [
                'label' => 'Couleur du texte',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
