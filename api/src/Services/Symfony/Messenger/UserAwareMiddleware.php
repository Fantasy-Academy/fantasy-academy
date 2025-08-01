<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Symfony\Messenger;

use FantasyAcademy\API\Entity\User;
use FantasyAcademy\API\Message\UserAware;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\StampInterface;

readonly final class UserAwareMiddleware implements MiddlewareInterface
{
    public function __construct(private Security $security) {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        if ($message instanceof UserAware) {
            /** @var null|User $user */
            $user = $this->security->getUser();

            if (null === $user) {
                throw new UnauthorizedHttpException('Bearer', 'Authentication required');
            }

            $message = $message->withUserId($user->id);

            // re-wrap the message preserving all existing stamps flattened into a single list
            /** @var StampInterface[][] $stampsByType */
            $stampsByType = $envelope->all();

            $flattenedStamps = [];
            foreach ($stampsByType as $stamps) {
                foreach ($stamps as $stamp) {
                    $flattenedStamps[] = $stamp;
                }
            }

            $envelope = Envelope::wrap($message, $flattenedStamps);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
