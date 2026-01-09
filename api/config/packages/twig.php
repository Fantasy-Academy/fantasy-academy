<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return App::config([
    'twig' => [
        'strict_variables' => true,
        'form_themes' => ['bootstrap_5_layout.html.twig'],
        'paths' => [
            '%kernel.project_dir%/assets/img' => 'images',
            '%kernel.project_dir%/assets/css' => 'styles',
        ],
    ],
]);
