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

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

return static function (DefinitionConfigurator $definition): void {
    $definition->rootNode()
        ->children()
            ->scalarNode('acc_enable')
                ->defaultFalse()
                ->info('Enable ACC [default to no]')
            ->end()
            ->scalarNode('acc_server')
                ->defaultValue('https://localhost')
                ->info('Url to ACC-Server')
            ->end()
            ->scalarNode('acc_software_token')
                ->defaultValue('SOFTWARE_APP_ID')
                ->info('Software AppId / SoftwareToken in ACC')
            ->end()
            ->scalarNode('acc_server_token')
                ->defaultValue('SERVER_TOKEN')
                ->info('Server token in ACC')
            ->end()
            ->scalarNode('acc_use_basic_auth')
                ->defaultFalse()
                ->info('Use BasicAuth [default to no]')
            ->end()
            ->scalarNode('acc_basic_auth_user')
                ->defaultValue('netbrothers')
                ->info('BasicAuth username')
            ->end()
            ->scalarNode('acc_basic_auth_password')
                ->defaultValue('password')
                ->info('BasicAuth username password')
            ->end()
        ->end()
    ;
};
