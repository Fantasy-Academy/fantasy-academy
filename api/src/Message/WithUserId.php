<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Message;

use ReflectionClass;
use Symfony\Component\Uid\Uuid;

/**
 * @property null|Uuid $userId
 */
trait WithUserId
{
    public function userId(): Uuid
    {
        assert($this->userId !== null);

        return $this->userId;
    }

    public function withUserId(Uuid $userId): static
    {
        $ref = new ReflectionClass($this);
        $ctor = $ref->getConstructor();

        if (null === $ctor) {
            throw new \LogicException('No constructor found on ' . $ref->getName());
        }

        // Build the ordered list of arguments to feed the ctor
        $args = [];

        foreach ($ctor->getParameters() as $param) {
            $name = $param->getName();

            if ($name === 'userId') {
                $args[] = $userId;
                continue;
            }

            // Read the existing property value
            $prop = $ref->getProperty($name);

            $args[] = $prop->getValue($this);
        }

        return $ref->newInstanceArgs($args);
    }
}
