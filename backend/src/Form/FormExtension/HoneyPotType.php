<?php

declare(strict_types=1);

namespace App\Form\FormExtension;

use App\EventSubscriber\Form\HoneyPotSubscriber;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class HoneyPotType extends AbstractType
{
    protected const DELICIOUS_HONEY_CANDY_FOR_BOTS = 'phone';
    protected const FABULOUS_HONEY_CANDY_FOR_BOTS = 'ville';

    public function __construct(
        private readonly LoggerInterface $honeyPotLogger,
        private readonly RequestStack $requestStack
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            child: self::DELICIOUS_HONEY_CANDY_FOR_BOTS,
            type: TextType::class,
            options: $this->setHoneyPotFieldsConfiguration()
        );

        $builder->add(
            child: self::FABULOUS_HONEY_CANDY_FOR_BOTS,
            type: TextType::class,
            options: $this->setHoneyPotFieldsConfiguration()
        );

        $builder->addEventSubscriber(
            subscriber: new HoneyPotSubscriber(
                honeyPotLogger: $this->honeyPotLogger,
                requestStack: $this->requestStack
            )
        );
    }

    /**
     * @description: Configuration des champs cachés
     * @return array<string, array<string, string>|false>
     */
    protected function setHoneyPotFieldsConfiguration(): array
    {
        return [
            'attr' => [
                'autocomplete' => 'off',
                'tabindex' => '-1',
            ],
            'mapped' => false,
            'required' => false,
        ];
    }
}
