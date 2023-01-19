<?php
declare(strict_types=1);

/**
 * This file is part of martin1982/live-broadcast-sonata-admin-bundle which is released under MIT.
 * See https://opensource.org/licenses/MIT for full license details.
 */
namespace Martin1982\LiveBroadcastSonataAdminBundle\Admin;

use Martin1982\LiveBroadcastBundle\Entity\Media\MediaFile;
use Martin1982\LiveBroadcastBundle\Entity\Media\MediaRtmp;
use Martin1982\LiveBroadcastBundle\Entity\Media\MediaUrl;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class InputAdmin
 */
class InputAdmin extends AbstractAdmin
{
    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    protected function configureFormFields(FormMapper $form): void
    {
        $subject = $this->getSubject();

        $form
            ->tab('General')
            ->with('General');

        if ($subject instanceof MediaFile) {
            $form->add('fileLocation', TextType::class, ['label' => 'File location on server']);
        }

        if ($subject instanceof MediaRtmp) {
            $form->add('rtmpAddress', TextType::class, ['label' => 'Address of the RTMP stream to repeat']);
        }

        if ($subject instanceof MediaUrl) {
            $form->add('url', TextType::class, ['label' => 'URL to video file']);
        }

        $form->end()
            ->end();
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    protected function configureListFields(ListMapper $list): void
    {
        $list->add('location', null, [
            'label' => 'Input',
            'accessor' => function ($subject) {
                return (string) $subject;
            },
        ]);
    }

    /**
     * @param bool $isChildAdmin
     *
     * @return string
     */
    protected function generateBaseRoutePattern(bool $isChildAdmin = false): string
    {
        return 'broadcast-input';
    }
}
