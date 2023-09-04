<?php

namespace App\Form\Account;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(child: 'email', type: EmailType::class, options: [
                'label' => null,
                'attr' => [
                    'placeholder' => 'Renseignez votre adresse email',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Vous devez renseigner votre adresse email.',
                    ]),
                    new Email([
                        'message' => 'Merci de saisir une adresse email valide.',
                    ]),
                ],
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
