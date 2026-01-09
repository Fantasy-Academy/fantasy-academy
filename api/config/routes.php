<?php

declare(strict_types=1);

namespace Symfony\Component\Routing\Loader\Configurator;

return Routes::config([
    'controllers' => [
        'resource' => '../src/Controller',
        'type' => 'attribute',
    ],
    'auth' => [
        'path' => '/api/login',
        'methods' => ['POST'],
    ],
    'logout' => [
        'path' => '/logout',
        'methods' => ['GET'],
    ],
]);
