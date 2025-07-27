<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Symfony\EventListener;

use FantasyAcademy\API\Exceptions\DomainException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsEventListener(event: KernelEvents::EXCEPTION, priority: 10)]
readonly final class DomainExceptionListener
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Unwrap Messenger failures
        if ($exception instanceof HandlerFailedException) {
            foreach ($exception->getWrappedExceptions() as $nested) {
                if ($nested instanceof DomainException) {
                    $this->convert($event, $nested);
                    return;
                }
            }
        }

        if ($exception instanceof DomainException) {
            $this->convert($event, $exception);
        }
    }

    private function convert(ExceptionEvent $event, DomainException $exception): void
    {
        $message = $exception->toHumanReadableMessage($this->translator);
        $statusCode = $exception->statusCode();

        $event->setThrowable(new HttpException($statusCode, $message, $exception));
    }
}
