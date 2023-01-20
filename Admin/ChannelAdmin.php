<?php
declare(strict_types=1);

/**
 * This file is part of martin1982/live-broadcast-sonata-admin-bundle which is released under MIT.
 * See https://opensource.org/licenses/MIT for full license details.
 */
namespace Martin1982\LiveBroadcastSonataAdminBundle\Admin;

use Martin1982\LiveBroadcastBundle\Entity\Channel\AbstractChannel;
use Martin1982\LiveBroadcastBundle\Entity\Channel\ChannelFacebook;
use Martin1982\LiveBroadcastBundle\Entity\Channel\ChannelYouTube;
use Martin1982\LiveBroadcastBundle\Entity\Channel\PlannedChannelInterface;
use Martin1982\LiveBroadcastBundle\Service\ChannelApi\ChannelApiStack;
use Martin1982\LiveBroadcastBundle\Service\ChannelApi\FacebookApiService;
use Martin1982\LiveBroadcastBundle\Service\ChannelApi\YouTubeApiService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\TemplateType;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class ChannelAdmin
 */
class ChannelAdmin extends AbstractAdmin
{
    /**
     * @var array
     */
    protected array $subclassConfigs = [];

    /**
     * @var ChannelApiStack
     */
    protected ChannelApiStack $channelApiStack;

    /**
     * Set configuration for the subclass configs
     *
     * @param array $configs
     */
    public function setSubclassConfigs(array $configs): void
    {
        $this->subclassConfigs = $configs;
    }

    /**
     * @param AbstractChannel[] $subclasses
     */
    public function setConfiguredSubclasses(array $subclasses): void
    {
        $configuredSubclasses = [];
        $config = $this->subclassConfigs;

        foreach ($subclasses as $classKey => $subclass) {
            if ($subclass::isEntityConfigured($config)) {
                $configuredSubclasses[$classKey] = $subclass;
            }
        }

        $this->setSubClasses($configuredSubclasses);
    }

    /**
     * @param ChannelApiStack $channelApiStack
     */
    public function setChannelApiServices(ChannelApiStack $channelApiStack): void
    {
        $this->channelApiStack = $channelApiStack;
    }

    /**
     * Configure extra admin routes.
     *
     * @param RouteCollectionInterface $collection
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('longLivedAccessToken', 'facebook/accesstoken');
        $collection->add('youtubeoauth', 'youtube/oauthprovider');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $subject = $this->getSubject();

        $nameClasses = 'generic-channel-name';
        if ($subject instanceof ChannelYouTube) {
            $nameClasses = 'generic-channel-name input-yt-channelname';
        }

        $form
            ->with('Channel')
                ->add('channelName', TextType::class, [
                    'label' => 'Channel name',
                    'attr' => ['class' => $nameClasses],
                ]);

        if (!$subject instanceof PlannedChannelInterface) {
            $form->add('streamKey', TextType::class, ['label' => 'Stream key']);
            $form->add('streamServer', TextType::class, ['label' => 'Stream server']);
        }

        if ($subject instanceof ChannelFacebook) {
            /** @var FacebookApiService $api */
            $api = $this->channelApiStack->getApiForChannel($subject);

            $form
                ->add('fbConnect', TemplateType::class, [
                    'template' => '@LiveBroadcastSonataAdmin/TemplateField/facebook_auth.html.twig',
                    'parameters' => [
                        'facebookAppId' => $api->getAppId(),
                    ],
                ])
                ->add('accessToken', HiddenType::class, [
                    'attr' => ['class' => 'fb-access-token'],
                ])
                ->add('fbEntityId', HiddenType::class, [
                    'attr' => ['class' => 'fb-entity-id'],
                ]);
        }

        if ($subject instanceof ChannelYouTube) {
            /** @var YouTubeApiService $api */
            $api = $this->channelApiStack->getApiForChannel($subject);

            $form
                ->add('fbConnect', TemplateType::class, [
                    'template' => '@LiveBroadcastSonataAdmin/TemplateField/youtube_auth.html.twig',
                    'parameters' => [
                        'facebookAppId' => $this->subclassConfigs['facebook']['application_id'],
                    ],
                ])
                ->add('youTubeChannelName', TextType::class, [
                    'attr' => [
                        'class' => 'input-yt-channelname',
                        'readonly' => 'readonly',
                        'label' => 'YouTube Channel Name',
                    ],
                ])
                ->add('refreshToken', TextType::class, [
                    'attr' => ['class' => 'input-yt-refreshtoken', 'readonly' => 'readonly'],
                ]);
        }

        $form->end();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('channelName')
            ->add('isHealthy');
    }

    /**
     * {@inheritdoc}
     *
     * @throws \RuntimeException
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('channelName')
            ->add('isHealthy')
            ->add('_action', 'actions', [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    /**
     * @param bool $isChildAdmin
     *
     * @return string
     */
    protected function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'channel';
    }
}
