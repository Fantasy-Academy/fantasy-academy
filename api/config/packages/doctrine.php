<?php

declare(strict_types=1);

use FantasyAcademy\API\Doctrine\ChoiceQuestionConstraintDoctrineType;
use FantasyAcademy\API\Doctrine\NumericQuestionConstraintDoctrineType;
use FantasyAcademy\API\Doctrine\UuidArrayDoctrineType;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {

    $containerConfigurator->extension('doctrine', [
        'dbal' => [
            'url' => '%env(resolve:DATABASE_URL)%',
            'types' => [
                UuidType::NAME => UuidType::class,
                UuidArrayDoctrineType::NAME => UuidArrayDoctrineType::class,
                NumericQuestionConstraintDoctrineType::NAME => NumericQuestionConstraintDoctrineType::class,
                ChoiceQuestionConstraintDoctrineType::NAME => ChoiceQuestionConstraintDoctrineType::class,
            ],
        ],
        'orm' => [
            'report_fields_where_declared' => true,
            'auto_generate_proxy_classes' => true,
            'naming_strategy' => 'doctrine.orm.naming_strategy.underscore_number_aware',
            'auto_mapping' => true,
            'controller_resolver' => [
                'auto_mapping' => false,
            ],
            'mappings' => [
                'FantasyAcademy' => [
                    'type' => 'attribute',
                    'dir' => '%kernel.project_dir%/src/Entity',
                    'prefix' => 'FantasyAcademy\\API\\Entity',
                ],
            ],
        ],
    ]);
};
