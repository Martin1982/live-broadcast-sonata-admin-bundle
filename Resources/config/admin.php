<?php declare(strict_types=1);

/**
 * This file is part of martin1982/live-broadcast-sonata-admin-bundle which is released under MIT.
 * See https://opensource.org/licenses/MIT for full license details.
 */

namespace Martin1982\LiveBroadcastSonataAdminBundle\DependencyInjection\Loader\Configurator;

use Martin1982\LiveBroadcastBundle\Entity\Channel\AbstractChannel;
use Martin1982\LiveBroadcastBundle\Entity\Channel\ChannelFacebook;
use Martin1982\LiveBroadcastBundle\Entity\Channel\ChannelTwitch;
use Martin1982\LiveBroadcastBundle\Entity\Channel\ChannelYouTube;
use Martin1982\LiveBroadcastBundle\Entity\LiveBroadcast;
use Martin1982\LiveBroadcastBundle\Entity\Media\AbstractMedia;
use Martin1982\LiveBroadcastBundle\Entity\Media\MediaFile;
use Martin1982\LiveBroadcastBundle\Entity\Media\MediaRtmp;
use Martin1982\LiveBroadcastBundle\Entity\Media\MediaUrl;
use Martin1982\LiveBroadcastSonataAdminBundle\Admin\ChannelAdmin;
use Martin1982\LiveBroadcastSonataAdminBundle\Admin\InputAdmin;
use Martin1982\LiveBroadcastSonataAdminBundle\Admin\LiveBroadcastAdmin;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/**
 * Return configuration closure
 *
 * @param ContainerConfigurator $container
 */
return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('admin.livebroadcast', LiveBroadcastAdmin::class)
        ->autowire()
        ->tag('sonata.admin', [
            'model_class' => LiveBroadcast::class,
            'manager_type' => 'orm',
            'group' => 'Live',
            'label' => 'Live Broadcasts',
        ])
        ->call('setTranslationDomain', [LiveBroadcastAdmin::class])
        ->call('setThumbnailPath', ['%livebroadcast.thumbnail.web_path%']);

    $services->set('admin.channel', ChannelAdmin::class)
        ->autowire()
        ->tag('sonata.admin', [
            'model_class' => AbstractChannel::class,
            'manager_type' => 'orm',
            'group' => 'Live',
            'label' => 'Channels',
        ])
        ->call('setSubclassConfigs', ['%livebroadcast.config%'])
        ->call('setConfiguredSubclasses', [[
            'Twitch' => ChannelTwitch::class,
            'Facebook' => ChannelFacebook::class,
            'YouTube' => ChannelYouTube::class,
        ]]);

    $services->set('admin.streaminput', InputAdmin::class)
        ->autowire()
        ->tag('sonata.admin', [
            'model_class' => AbstractMedia::class,
            'manager_type' => 'orm',
            'group' => 'Live',
            'label' => 'Inputs',
        ])
        ->call('setSubClasses', [[
            'File' => MediaFile::class,
            'URL' => MediaUrl::class,
            'RTMP' => MediaRtmp::class,
        ]]);
};
