<?php

namespace Splash\Sylius\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Splash Sylius Bundle Configuration
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
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
            ->end()
        ;

        return $treeBuilder;
    }
}
