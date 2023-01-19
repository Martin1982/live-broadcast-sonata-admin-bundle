<?php declare(strict_types=1);

/**
 * This file is part of martin1982/live-broadcast-sonata-admin-bundle which is released under MIT.
 * See https://opensource.org/licenses/MIT for full license details.
 */
namespace Martin1982\LiveBroadcastSonataAdminBundle\Admin\Block;

use Martin1982\LiveBroadcastBundle\Exception\LiveBroadcastException;
use Martin1982\LiveBroadcastBundle\Service\ChannelApi\Client\GoogleClient;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Class YouTubeBlockService
 */
class YouTubeBlockService extends AbstractBlockService
{
    /**
     * YouTubeBlockService constructor
     *
     * @param Environment  $twig
     * @param GoogleClient $googleClient
     * @param RequestStack $requestStack
     */
    public function __construct(Environment $twig, protected GoogleClient $googleClient, protected RequestStack $requestStack)
    {
        parent::__construct($twig);
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     *
     * @return Response
     *
     * @throws LiveBroadcastException
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        $client = $this->googleClient->getClient();
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            $request = new Request();
        }

        $session = $request->getSession();
        $refreshToken = $session->get('youTubeRefreshToken');
        if ($refreshToken) {
            $client->fetchAccessTokenWithRefreshToken($refreshToken);
        }

        $accessToken = $client->getAccessToken();
        $isAuthenticated = (bool) $accessToken;
        $state = mt_rand();

        if (!$isAuthenticated) {
            $session->set('state', $state);
            $session->set('authreferer', $request->getRequestUri());
        }

        $client->setState($state);

        return $this->renderResponse(
            'LiveBroadcastSonataAdminBundle:Block:youtube_auth.html.twig',
            [
                'isAuthenticated' => $isAuthenticated,
                'authUrl' => $isAuthenticated ? '#' : $client->createAuthUrl(),
                'youTubeChannelName' => $session->get('youTubeChannelName'),
                'youTubeRefreshToken' => $session->get('youTubeRefreshToken'),
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                ],
            $response
        );
    }
}
