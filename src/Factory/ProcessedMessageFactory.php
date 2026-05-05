<?php

namespace App\Factory;

use App\Entity\ProcessedMessage;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<ProcessedMessage>
 */
final class ProcessedMessageFactory extends PersistentObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return ProcessedMessage::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'runId' => self::faker()->randomNumber(),
            'attempt' => 1,
            'type' => \stdClass::class,
            'description' => self::faker()->text(255),
            'dispatchedAt' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'receivedAt' => clone \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'finishedAt' => clone \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'memoryUsage' => self::faker()->randomNumber(),
            'transport' => self::faker()->text(255),
            'tags' => self::faker()->text(255),
            'waitTime' => self::faker()->randomNumber(),
            'handleTime' => self::faker()->randomNumber(),
            'results' => [],
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->instantiateWith(function(array $attributes, string $class) {
                $reflection = new \ReflectionClass($class);
                $object = $reflection->newInstanceWithoutConstructor();
                
                foreach ($attributes as $property => $value) {
                    $currentClass = $reflection;
                    while ($currentClass !== false) {
                        if ($currentClass->hasProperty($property)) {
                            $prop = $currentClass->getProperty($property);
                            $prop->setAccessible(true);
                            $prop->setValue($object, $value);
                            break;
                        }
                        $currentClass = $currentClass->getParentClass();
                    }
                }
                
                return $object;
            })
            // ->afterInstantiate(function(ProcessedMessage $processedMessage): void {})
        ;
    }
}
