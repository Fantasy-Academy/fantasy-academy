<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Symfony\EventListener;

use Sentry\State\HubInterface;
use Sentry\State\Scope;
use Sentry\Tracing\PropagationContext;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 512)]
readonly final class SentryScopeResetListener
{
    public function __construct(
        private HubInterface $hub,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if ($event->isMainRequest() === false) {
            return;
        }

        $this->hub->configureScope(static function (Scope $scope): void {
            // Clear all accumulated state: breadcrumbs, tags, contexts, extra, user, fingerprint, level, span, flags
            $scope->clear();

            // Reset propagation context to get fresh trace/span IDs for this request
            $scope->setPropagationContext(PropagationContext::fromDefaults());
        });
    }
}
