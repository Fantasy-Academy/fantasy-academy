<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

return Routes::config([
    '_errors' => [
        'resource' => '@FrameworkBundle/Resources/config/routing/errors.php',
        'prefix' => '/_error',
    ],
]);
