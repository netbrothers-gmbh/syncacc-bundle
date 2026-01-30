<?php

declare(strict_types=1);

/*
 * This file is part of the NetBrothers SyncAccBundle.
 *
 * (c) 2024 NetBrothers GmbH | Stefan Wessel (https://netbrothers.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use NetBrothers\SyncAccBundle\Command\SyncCommand;
use NetBrothers\SyncAccBundle\Services\AccService;
use NetBrothers\SyncAccBundle\Services\ConfigService;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    // Config Service
    $services->set(ConfigService::class)
        ->arg('$config', param('net_brothers_sync_acc'));

    // Load all other services
    $services->load('NetBrothers\\SyncAccBundle\\', '../src/*')
        ->exclude('../src/{DependencyInjection,Entity,Tests,Services/ConfigService.php}');

    // Explicitly define public services if needed
    $services->set(AccService::class)
        ->public();

    $services->set(SyncCommand::class)
        ->public();
};
