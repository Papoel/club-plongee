<?php

namespace App\Form;

use App\Entity\Certificate;
use App\EventSubscriber\Form\CertificateExpirationDateOnLastDayOfYear;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class CertificateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'medicalCertificateFile', type: VichFileType::class, options: [
                'label' => false,
                'required' => false,
                'mapped' => false,
                'allow_delete' => false,
                'download_uri' => false,
            ])

            // Afficher le champ expireAt en Année uniquement puis transformer la date en DateTimeImmutable
            ->add(child: 'expireAt', type: DateType::class, options: [
                'label' => false,
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy',
                'html5' => false,
                'attr' => [
                    'class' => 'js-datepicker',
                    'placeholder' => 'Année',
                ],
            ])

            ->addEventSubscriber(subscriber: new CertificateExpirationDateOnLastDayOfYear())
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Certificate::class,
            // 🚥 commenter pour réactiver la validation html5 !
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
