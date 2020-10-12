<?php declare(strict_types=1);

/**
 * This file is part of martin1982/live-broadcast-sonata-admin-bundle which is released under MIT.
 * See https://opensource.org/licenses/MIT for full license details.
 */
namespace Martin1982\LiveBroadcastSonataAdminBundle\Admin\Block;

use Martin1982\LiveBroadcastSonataAdminBundle\Admin\ChannelAdmin;
use Martin1982\LiveBroadcastBundle\Service\ChannelApi\FacebookApiService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Class FacebookBlockService
 */
class FacebookBlockService extends AbstractBlockService
{
    /**
     * @var FacebookApiService
     */
    protected $apiService;

    /**
     * @var ChannelAdmin
     */
    protected $admin;

    /**
     * FacebookBlockService constructor
     *
     * @param Environment        $twig
     * @param FacebookApiService $apiService
     * @param ChannelAdmin       $admin
     */
    public function __construct(Environment $twig, FacebookApiService $apiService, ChannelAdmin $admin)
    {
        $this->apiService = $apiService;
        $this->admin = $admin;

        parent::__construct($twig);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter('blockContext'))
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        return $this->renderResponse(
            'LiveBroadcastSonataAdminBundle:Block:facebook_auth.html.twig',
            [
                'facebookAppId' => $this->apiService->getAppId(),
                'admin' => $this->admin,
            ],
            $response
        );
    }
}
