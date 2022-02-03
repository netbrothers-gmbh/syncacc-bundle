<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 24.03.21
 *
 */

namespace NetBrothers\SyncAccBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class NetBrothersSyncAccExtension
 * @package NetBrothers\SyncAccBundle\DependencyInjection
 */
class NetBrothersSyncAccExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . "/../Resources/config"));
        $configuration = new Configuration();
        $loader->load('services.xml');
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('acc', $config);

        $syncAccService = $container->getDefinition('netbrothers_syncacc.command.sync_command');
        $syncAccService->setArgument(1, $container->getParameter('acc'));
    }

    public function getAlias(): string
    {
        return 'netbrothers_syncacc';
    }
}
