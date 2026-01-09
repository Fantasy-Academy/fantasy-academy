<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('twig', [
        'strict_variables' => true,
        'form_themes' => ['bootstrap_5_layout.html.twig'],
        'paths' => [
            '%kernel.project_dir%/assets/img' => 'images',
            '%kernel.project_dir%/assets/css' => 'styles',
        ],
    ]);
};
