<?php

namespace Imho\ImageUploadBundle;

use Imho\ImageUploadBundle\Form\ImageUploadType;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * Provides image upload support: the ImageUploader service and the ImageUploadType form field.
 *
 * Configurable through config/packages/image_upload.yaml (upload_dir, max_size, mime_types).
 */
class ImageUploadBundle extends AbstractBundle
{
    /**
     * Defines the bundle's configuration options and their defaults.
     * SVG is deliberately excluded from the default MIME types, because SVG files can contain scripts.
     */
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('upload_dir')->defaultValue('%kernel.project_dir%/public/uploads')->end()
                ->scalarNode('max_size')->defaultValue('2M')->end()
                ->arrayNode('mime_types')
                    ->scalarPrototype()->end()
                    ->defaultValue(['image/jpeg', 'image/png', 'image/webp'])
                ->end()
            ->end();
    }

    /**
     * Registers the uploader and form type as services, passing in the configured values.
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->services()
            ->set(ImageUploader::class)
                ->args([$config['upload_dir']])
            ->set(ImageUploadType::class)
                ->args([$config['max_size'], $config['mime_types']])
                ->tag('form.type');
    }
}