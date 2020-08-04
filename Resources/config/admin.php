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
use Martin1982\LiveBroadcastSonataAdminBundle\Admin\Block\FacebookBlockService;
use Martin1982\LiveBroadcastSonataAdminBundle\Admin\Block\YouTubeBlockService;
use Martin1982\LiveBroadcastSonataAdminBundle\Admin\ChannelAdmin;
use Martin1982\LiveBroadcastSonataAdminBundle\Admin\InputAdmin;
use Martin1982\LiveBroadcastSonataAdminBundle\Admin\LiveBroadcastAdmin;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Return configuration closure
 *
 * @param ContainerConfigurator $container
 */
return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->set('sonata.admin.livebroadcast', LiveBroadcastAdmin::class)
        ->args([
            null,
            LiveBroadcast::class,
            null,
        ])
        ->tag('sonata.admin', [
            'manager_type' => 'orm',
            'group' => 'Live',
            'label' => 'Live Broadcasts',
        ])
        ->call('setTranslationDomain', [LiveBroadcastAdmin::class])
        ->call('setThumbnailPath', ['%livebroadcast.thumbnail.web_path%']);

    $services->set('sonata.admin.channel', ChannelAdmin::class)
        ->args([
            null,
            AbstractChannel::class,
            'LiveBroadcastSonataAdminBundle:CRUD',
        ])
        ->tag('sonata.admin', [
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

    $services->set('sonata.admin.streaminput', InputAdmin::class)
        ->args([
            null,
            AbstractMedia::class,
            null,
        ])
        ->tag('sonata.admin', [
            'manager_type' => 'orm',
            'group' => 'Live',
            'label' => 'Inputs',
        ])
        ->call('setSubClasses', [[
            'File' => MediaFile::class,
            'URL' => MediaUrl::class,
            'RTMP' => MediaRtmp::class,
        ]]);

    $services->set('sonata.block.service.youtubeauth', YouTubeBlockService::class)
        ->args([
            'sonata.block.service.youtubeauth',
            new Reference('templating.engine.twig'),
            new Reference('live.broadcast.channel_api.client.google'),
            new Reference('request_stack'),
        ])
        ->tag('sonata.block');

    $services->set('sonata.block.service.facebookauth', FacebookBlockService::class)
        ->args([
            'sonata.block.service.facebookauth',
            new Reference('templating.engine.twig'),
            new Reference('live.broadcast.facebook_api.service'),
            new Reference('sonata.admin.channel'),
        ])
        ->tag('sonata.block');
};
