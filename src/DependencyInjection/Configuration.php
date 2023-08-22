<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 24.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package NetBrothers\SyncAccBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('netbrothers_syncacc');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->booleanNode('acc_enable')
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
                ->booleanNode('acc_use_basic_auth')
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
        return $treeBuilder;
    }
}
