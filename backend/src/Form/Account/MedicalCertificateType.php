<?php

declare(strict_types=1);

namespace App\Form\Account;

use App\Entity\User;
use App\EventSubscriber\Form\CertificateExpirationDateOnLastDayOfYear;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

class MedicalCertificateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(child: 'medicalCertificateFile', type: VichFileType::class, options: [
            'label' => false,
            'required' => false,
            'mapped' => false,
            'allow_delete' => false,
            'download_uri' => false,
            'constraints' => [
                new File([
                    'maxSize' => '2M',
                    'mimeTypes' => [
                        'application/pdf',
                        'application/x-pdf',
                    ],
                    'mimeTypesMessage' => 'Le fichier doit être au format PDF',
                    'maxSizeMessage' => 'Le fichier ne doit pas dépasser 2 Mo',
                ]),
            ],
        ]);

        $builder->addEventSubscriber(
            subscriber: new CertificateExpirationDateOnLastDayOfYear(),
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['user' => User::class,
            // 🚥 commenter pour réactiver la validation html5 !
            'attr' => ['novalidate' => 'novalidate']]);
    }
}
