<?php

namespace Rabble\UserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Vich\UploaderBundle\Naming\SmartUniqueNamer;

class RabbleUserExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(\dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $this->prependVichUploader($container);
    }

    private function prependVichUploader(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('vich_uploader', [
            'mappings' => [
                'user' => [
                    'db_driver' => 'orm',
                    'uri_prefix' => '/uploads/user',
                    'upload_destination' => '%kernel.project_dir%/public/uploads/user',
                    'namer' => SmartUniqueNamer::class,
                ],
            ],
        ]);
    }
}
