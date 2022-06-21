# Sylius-Bundle
Splash Bundle for Sylius E-Commerce Solution


[![N|Solid](https://github.com/SplashSync/Php-Core/blob/master/Resources/img/fake-image2.jpg)](http://www.splashsync.com)
# Splash Sync Bundle for Sylius E-Commerce
Splash Bundle for Sylius E-Commerce Solution

This module implement Splash Sync connector for Sylius, the E-Commerce Solution for Symfony Framework. 
It provides access to multiples Objects for automated synchronization through Splash Sync dedicated protocol.

[![Latest Stable Version](https://poser.pugx.org/splash/sylius-bundle/v/stable)](https://packagist.org/packages/splash/sylius-splash-plugin)
[![Total Downloads](https://poser.pugx.org/splash/sylius-bundle/downloads)](https://packagist.org/packages/splash/sylius-splash-plugin)
[![License](https://poser.pugx.org/splash/sylius-bundle/license)](https://packagist.org/packages/splash/sylius-splash-plugin)

## Installation via Composer

Download Sylius-Bundle and its dependencies to the vendor directory. You can use Composer for the automated process:

```bash
$ php composer.phar require splash/sylius-splash-plugin
```

Composer will install the bundle to `vendor/splash` directory.

### Adding bundle to your application kernel

```php

// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
            new \Splash\Bundle\SplashBundle(),                          // Splash Sync Core PHP Bundle 
            new \Splash\SyliusSplashPlugin\SplashSyliusSplashPlugin(),  // Splash Bundle for Sylius
        // ...
    );
}

```

### Configure Splash Bundles

Here is the default configuration for Splash bundles:

```yml

    splash:
        id:             ThisIsSyliusWsId                # Your Splash Server ID
        key:            ThisIsSyliusWsEncryptionKey     # Your Server Secret Encryption Key

    splash_sylius_splash:
        default_channel:    FASHION_WEB                 # Select here your shop default channel

```

## Requirements

* PHP 7.4+
* Sylius 1.6+
* An active Splash Sync User Account

## Documentation

For the configuration guide and reference, see: [Sylius Bundle Docs](https://splashsync.github.io/Sylius-Bundle/)

## Contributing

Any Pull requests are welcome! 

This module is part of [SplashSync](http://www.splashsync.com) project.