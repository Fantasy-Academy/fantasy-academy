<?php

declare(strict_types=1);

use Symfony\Config\TwigConfig;

return static function (TwigConfig $twig): void {
    $twig->strictVariables(true);

    $twig->formThemes(['bootstrap_5_layout.html.twig']);

    $twig->path('%kernel.project_dir%/assets/img', 'images');
    $twig->path('%kernel.project_dir%/assets/css', 'styles');
};
