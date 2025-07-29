<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use FantasyAcademy\API\Api\ApiResource\AnswerQuestionRequest;
use FantasyAcademy\API\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * @implements ProcessorInterface<AnswerQuestionRequest, null>
 */
readonly final class AnswerQuestionProcessor implements ProcessorInterface
{
    public function __construct(
        // private MessageBusInterface $bus,
        private Security $security,
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $token = $this->security->getToken();
        if (null === $token || null === $token->getUser()) {
            throw new UnauthorizedHttpException('Bearer', 'Authentication required');
        }

        $user = $token->getUser();
        assert($user instanceof User);

        // Dispatch pure application command
        /*
        $this->bus->dispatch(new \FantasyAcademy\API\Application\Command\CompleteChallenge(
            $data->name,
            $userId
        ));
        */

        return null;
    }
}
