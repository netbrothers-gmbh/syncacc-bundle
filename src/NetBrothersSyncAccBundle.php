<?php
/**
 * NetBrothers Sync Access Control Center
 *
 * @author Stefan Wessel, NetBrothers GmbH
 * @date 24.03.21
 *
 */

namespace NetBrothers\SyncAccBundle;

use NetBrothers\SyncAccBundle\DependencyInjection\NetBrothersSyncAccExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NetBrothersSyncAccBundle
 * @package NetBrothers\SyncAccBundle
 */
class NetBrothersSyncAccBundle extends Bundle
{
    /**
     * Overridden to allow for the custom extension alias.
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new NetBrothersSyncAccExtension();
        }
        return $this->extension;
    }
}
