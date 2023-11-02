<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\SyliusSplashPlugin\DependencyInjection;

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
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('splash_sylius_splash');

        // @phpstan-ignore-next-line
        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('default_channel')
            ->defaultValue("default")
            ->cannotBeEmpty()
            ->info('Default Channel for association with new Products.')
            ->end()
            ->arrayNode('units')->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('weights')->defaultValue("kg")->end()
            ->scalarNode('dimensions')->defaultValue("m")->end()
            ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
