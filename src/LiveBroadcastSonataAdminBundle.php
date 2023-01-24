<?php
declare(strict_types=1);

/**
 * This file is part of martin1982/live-broadcast-sonata-admin-bundle which is released under MIT.
 * See https://opensource.org/licenses/MIT for full license details.
 */
namespace Martin1982\LiveBroadcastSonataAdminBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Class LiveBroadcastBundle.
 */
class LiveBroadcastSonataAdminBundle extends AbstractBundle
{
    /**
     * {@inheritDoc}
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/admin.php');
    }
}
