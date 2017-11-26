
[![N|Solid](https://github.com/SplashSync/Php-Core/raw/master/Resources/img/fake-image2.jpg)](http://www.splashsync.com)

# Splash Bundle for Sylius

Welcome to the Sylius Bundle's Documentation!

This module implement Splash Sync connector for Sylius, the E-Commerce Solution for Symfony Framework. It provide access to multiples Objects for automated synchonization through Splash Sync dedicated protocol.

[![Build Status](https://travis-ci.org/SplashSync/Sylius-Bundle.svg?branch=master)](https://travis-ci.org/SplashSync/Sylius-Bundle)
[![Latest Stable Version](https://poser.pugx.org/splash/sylius-bundle/v/stable)](https://packagist.org/packages/splash/sylius-bundle)
[![Total Downloads](https://poser.pugx.org/splash/sylius-bundle/downloads)](https://packagist.org/packages/splash/sylius-bundle)
[![License](https://poser.pugx.org/splash/sylius-bundle/license)](https://packagist.org/packages/splash/sylius-bundle)

## Installation

* Download latest stable version here : [Splash Modules](http://www.splashsync.com/en/modules/)
* Copy module contents on Wordpress plugin folder (wp-content/plugins/splash) 
* Enable & Configure the module

### Composer

Download Sylius-Bundle and its dependencies to the vendor directory. You can use Composer for the automated process:

```bash
$ php composer.phar require splash/sylius-bundle
```

Composer will install the bundle to `vendor/splash` directory.

### Adding bundle to your application kernel

```php

// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
            new \Splash\Bundle\SplashBundle(),          // Splash Sync Core PHP Bundle 
            new \Splash\Sylius\SplashSyliusBundle(),    // Splash Bundle for Sylius
        // ...
    );
}

```

### Configure Splash Bundles

Here is the default configuration for Splash bundles:

```yml

    splash:
        id:             812b124aa746e04c                                        # Your Splash Server Id
        key:            NTdlMDI2YWQ1NTQ5NjAuOTI3OTgxMTQ1N2UwMjZhZDU1NjFiMS      # Your Server Secret Encryption Key

    splash_sylius:
        default_channel:    US_WEB      # Select here you shop's default channel

```

## Requirements

* PHP 7.0+
* Sylius 1.0+
* An active Splash Sync User Account

## Documentation

For the configuration guide and reference, see: [Sylius Bundle Wiki](https://github.com/SplashSync/Silius/wiki)

## Contributing

Any Pull requests are welcome! 

This module is part of [SplashSync](http://www.splashsync.com) project.