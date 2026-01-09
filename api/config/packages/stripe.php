<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Stripe\StripeClient;

return App::config([
    'services' => [
        'stripe.client' => [
            'class' => StripeClient::class,
            'arguments' => ['%env(STRIPE_SECRET_KEY)%'],
        ],
        StripeClient::class => '@stripe.client',
    ],
]);
