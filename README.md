# Live Broadcast Sonata Admin Bundle

[![Build status](https://github.com/martin1982/live-broadcast-sonata-admin-bundle/workflows/Static%20analysis%20of%20live-broadcast-sonata-admin-bundle/badge.svg)](https://github.com/martin1982/live-broadcast-sonata-admin-bundle/workflows/Static%20analysis%20of%20live-broadcast-sonata-admin-bundle/badge.svg)
[![Latest stable version](https://poser.pugx.org/martin1982/live-broadcast-sonata-admin-bundle/v/stable)](https://packagist.org/packages/martin1982/live-broadcast-sonata-admin-bundle)

[![License](https://poser.pugx.org/martin1982/live-broadcast-sonata-admin-bundle/license)](https://packagist.org/packages/martin1982/live-broadcast-sonata-admin-bundle)
[![Total downloads](https://poser.pugx.org/martin1982/live-broadcast-sonata-admin-bundle/downloads)](https://packagist.org/packages/martin1982/live-broadcast-sonata-admin-bundle)

## Table of contents

- [About](#about)
- [Prerequisites](#prerequisites)
- [Basic installation](#basic-installation)
- [Enabling Facebook Live](#enabling-facebook-live)
- [Enabling YouTube Live](#enabling-youtube-live)
- [Add new output platforms](#add-new-output-platforms)
- [Admin GUI support](#admin-gui-support)

## About

The Live Broadcast Bundle will make it possible to plan live video streams to
various channels like Twitch, YouTube Live, Facebook Live (referred to as Output or Channels). 

As "Input" we support files, URLs or existing RTMP streams.

For more info you can view the latest recorded presentation below, check the demo project at https://github.com/Martin1982/live-broadcast-demo or read on;

[![IMAGE ALT TEXT](http://img.youtube.com/vi/axutXblArhM/0.jpg)](http://www.youtube.com/watch?v=axutXblArhM "High quality live broadcasting with PHP by @Martin1982 at @PHPamersfoort")

## Prerequisites

The Broadcaster needs a few commands;

* `ffmpeg 3.x or higher`

On Linux:
* `ps`
* `kill`

On Mac:
* `ps`
* `grep`
* `kill`

On Windows:
* `tasklist`
* `taskkill`

To test these prerequisites the Symfony command `livebroadcaster:test:shell` can be used after the installation described below.

## Basic installation

This bundle will be made available on Packagist. You can then install it using Composer:

```bash
$ composer require martin1982/live-broadcast-bundle
```

Next, for Symfony \< 4 enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Martin1982\LiveBroadcastBundle\LiveBroadcastBundle(),
    );
}
```

Use Doctrine to update your database schema with the broadcasting entities, when upgrading it is recommended to use migrations.

To start the broadcast scheduler you can run the following command:

```bash
$ php app/console livebroadcaster:broadcast
```

To make broadcast planning available through an admin interface we've added support for the Sonata Admin bundle.

### FFMpeg log directory
To view the output of FFMpeg you need to configure a log directory in your `app/config/config.yml`.
 
     live_broadcast:
        ffmpeg:
            log_directory: '%kernel.logs_dir%'

### Event loop
You can use this configuration to set the event loop timer:

    live_broadcast:
        eventloop:
            timer: 5

### Thumbnail setup
Set up the following config for thumbnails:
    
    live_broadcast:
        thumbnail:
            upload_directory: '%kernel.root_dir%/../web/uploads/thumbnails'
            web_path: '/uploads/thumbnails'

## Enabling Facebook Live
Create a Facebook app on https://developers.facebook.com with the following permissions:

- user_videos
- user_events
- user_managed_groups
- manage_pages
- publish_actions
- Live-Video API

Edit your `app/config/config.yml` with the following configuration:

    live_broadcast:
        facebook:
            application_id: YourFacebookAppId
            application_secret: YourFacebookAppSecret

When using Sonata Admin; add the Sonata block to your `blocks` config:

    sonata_block:
        blocks:
        sonata.block.service.facebookauth:
            contexts:   [admin]

## Enabling YouTube Live

Login to https://console.developers.google.com/ and enable the 'YouTube Data API v3'.

Setup oAuth Credentials for your server. In case you're using the Sonata Admin from this
bundle the redirect URI's path is `<your domain>/admin/channel/youtube/oauthprovider`

Add the YouTube API info to your config.yml:

    live_broadcast:
        youtube:
            client_id: YourGoogleOauthClientId
            client_secret: YourGoogleOauthClientSecret
            redirect_route: admin_martin1982_livebroadcast_channel_abstractchannel_youtubeoauth

 
When using Sonata Admin; add the Sonata block to your `blocks` config:

    sonata_block:
        blocks:
        sonata.block.service.youtubeauth:
            contexts:   [admin]
             
Add these lines to your parameters.yml (used for generating a thumbnail URL)

    parameters:
        router.request_context.host: broadcast.com
        router.request_context.scheme: https
    
## Add new output platforms

Create a new Channel Entity in Entity/Channel that extends the AbstractChannel (e.g. ChannelNew)

Create a new StreamOutput service in Service/StreamOutput that implements the OutputInterface (e.g. OutputNew)

Configure the service with the output tag in Resources/config/services.yml

    live.broadcast.output.new:
        class: Martin1982\LiveBroadcastBundle\Service\StreamOutput\OutputNew
        tags:
            - { name: live.broadcast.output, platform: 'New' }

To add support for Sonata admin; add a new form for the Channel in Admin/ChannelAdmin.php

``` php
protected function configureFormFields(FormMapper $formMapper)
{
    if ($subject instanceof ChannelNew) {
        $formMapper->add('...', 'text', array('label' => '...'));
    }
}
```

Next add the subclass for the channelAdmin in Resources/config/admin.yml for 

    sonata.admin.channel
        calls:
            - [setConfiguredSubclasses, [ { "Name": Martin1982\LiveBroadcastBundle\Entity\Channel\ChannelNew } ] ]

# Admin GUI support

This package is created to support Sonata Admin for the Web GUI interface, there are other flavours available from
Packagist with no GUI (the base martin1982/live-broadcast-bundle package) and EasyAdmin (martin1982/live-broadcast-easyadmin-bundle). 