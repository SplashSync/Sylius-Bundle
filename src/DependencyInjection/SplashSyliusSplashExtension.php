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

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Splash Sylius Bundle Extensions Configurator
 */
class SplashSyliusSplashExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        // Inject List of Symfony Locales in Bundle Config
        $config["locale"] = $container->getParameter('locale');
        // Inject Path for Sylius Images in Bundle Config
        /** @var string $publicDir */
        $publicDir = $container->getParameter('sylius_core.public_dir');
        $config["images_folder"] = $publicDir."/media/image/";

        $container->setParameter('splash_sylius', $config);
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        /** @var array[] $splashConfigs */
        $splashConfigs = $container->getExtensionConfig('splash');
        //====================================================================//
        // Override Configuration to Setup Icon & Logo Urls
        foreach ($splashConfigs as $splashConfig) {
            foreach ($splashConfig['connections'] ?? array() as $name => $connection) {
                if (isset($connection['config']["infos"])) {
                    continue;
                }
                $container->prependExtensionConfig('splash', array(
                    'connections' => array(
                        $name => array(
                            'config' => array(
                                'infos' => array(
                                    'ico' => dirname(__DIR__)."/Resources/public/sylius-logo.svg",
                                    'logo' => "/bundles/splashsyliussplashplugin/sylius-logo.png",
                                )
                            )
                        )
                    )
                ));
            }
        }
    }
}
