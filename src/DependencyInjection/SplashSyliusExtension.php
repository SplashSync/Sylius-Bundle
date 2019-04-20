<?php

namespace Splash\Sylius\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SplashSyliusExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        
        // Inject List of Symfony Locales in Bundle Config
        $config["locale"] = $container->getParameter('locale');
        // Inject Path for Sylius Images in Bundle Config
        $config["images_folder"] = $container->getParameter('sylius_core.public_dir') . "/media/image/";
        
        $container->setParameter('splash_sylius', $config);
    }
}
