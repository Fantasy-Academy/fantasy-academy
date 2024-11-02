<?php declare(strict_types=1);

use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Config\FrameworkConfig;
use FantasyAcademy\API\Message\Product\ProcessProductImport;

return static function (FrameworkConfig $framework): void {
    $messenger = $framework->messenger();

    $bus = $messenger->bus('command_bus');
    $bus->middleware()->id('doctrine_transaction');

    $messenger->failureTransport('failed');

    $messenger->transport('sync')
        ->dsn('sync://');

    $messenger->transport('failed')
        ->dsn('doctrine://default?queue_name=failed');

    $messenger->transport('async')
        ->options([
            'auto_setup' => false,
            'use_notify' => true,
            'check_delayed_interval' => 2000,
        ])
        ->dsn('%env(MESSENGER_TRANSPORT_DSN)%');

    $messenger->routing('FantasyAcademy\API\Events\*')->senders(['async']);
    $messenger->routing(SendEmailMessage::class)->senders(['async']);
};
