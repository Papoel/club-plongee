<?php

declare(strict_types=1);

namespace App\Tests\Abstract;

use Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class EntityTestCase extends KernelTestCase
{
    /**
     * @throws Exception
     */
    public function assertValidationErrorsCount(object $entity, int $count, ?string $expectedMessage = null): void
    {
        self::bootKernel();

        $container = self::getContainer();
        $validator = $container->get(ValidatorInterface::class);
        $violations = $validator->validate($entity);

        $messages = [];
        foreach ($violations as $violation) {
            $messages[] =
                'Erreur sur la Propriété '
                . ucfirst($violation->getPropertyPath()) . ' => ' . $violation->getMessage();
        }

        self::assertCount(
            expectedCount: $count,
            haystack: $violations,
            message: implode(separator:PHP_EOL, array: $messages)
        );

        /*if ($expectedMessage !== null) {
            self::assertStringContainsString(
                $expectedMessage,
                haystack: implode(separator: PHP_EOL, array: $messages)
            );
        }*/

        if ($expectedMessage !== null && !in_array(needle: $expectedMessage, haystack: $messages, strict: true)) {
            $this->fail("Le message d'erreur '$expectedMessage' n'a pas été trouvé dans les violations.");
        }
    }

    /**
     * @throws Exception
     */
    public function validateEntity(object $entity): ConstraintViolationListInterface
    {
        self::bootKernel();

        $container = self::getContainer();
        return $container->get(ValidatorInterface::class)->validate($entity);
    }

    public function assertContainsViolation(string $message, ConstraintViolationListInterface $violations): void
    {
        foreach ($violations as $violation) {
            if ($violation->getMessage() === $message) {
                return;
            }
        }

        $this->fail("Le message d'erreur '$message' n'a pas été trouvé dans les violations.");
    }
}
