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
        
        $container->setParameter('splash_sylius',    $config);
        
        //====================================================================//
        // Add Bundle Objects to Splash Parameters
        $Splash                 =   $container->getParameter('splash');
        $Splash["objects"][]    =   "Splash\Sylius\Objects\Address";
        $Splash["objects"][]    =   "Splash\Sylius\Objects\Customer";
//        $Splash["objects"][]    =   "Splash\Sylius\Objects\Product";
        $Splash["objects"][]    =   "Splash\Sylius\Objects\Order";

        //====================================================================//
        // Add Bundle Widgets to Splash Parameters
        $Splash["widgets"][]    =   "Splash\Local\Widgets\DefaultWidget";
        $Splash["widgets"][]    =   "Splash\Local\Widgets\SelfTest";
        
        //====================================================================//
        // Update Splash Bundle Parameters
        $container->setParameter('splash',$Splash);
        
    }
}