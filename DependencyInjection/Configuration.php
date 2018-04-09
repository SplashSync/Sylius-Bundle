<?php

namespace Splash\Sylius\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('splash_sylius');

        $rootNode
            ->children()
                //====================================================================//
                // COMMON Parameters
                //====================================================================//
                ->scalarNode('default_channel')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->defaultValue("default")
                    ->info('Default Channel for association with new Products.')
                ->end()
                ->scalarNode('images_folder')
                    ->cannotBeEmpty()
                     ->defaultValue("%kernel.root_dir%/../web/media/image")
                    ->info('Default Folder for Storage of new Images.')
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
