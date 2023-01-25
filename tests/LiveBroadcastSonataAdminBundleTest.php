<?php
declare(strict_types=1);

/**
 * This file is part of martin1982/live-broadcast-sonata-admin-bundle which is released under MIT.
 * See https://opensource.org/licenses/MIT for full license details.
 */
namespace Martin1982\LiveBroadcastSonataAdminBundle\Tests;

use Martin1982\LiveBroadcastSonataAdminBundle\LiveBroadcastSonataAdminBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class LiveBroadcastSonataAdminBundleTest
 */
class LiveBroadcastSonataAdminBundleTest extends TestCase
{
    /**
     * Test building the bundle
     */
    public function testBuild(): void
    {
        $container = $this->createMock(ContainerBuilder::class);

        $bundle = new LiveBroadcastSonataAdminBundle();

        $bundle->build($container);
        $this->addToAssertionCount(1);
    }
}
