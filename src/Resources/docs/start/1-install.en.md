---
lang: en
permalink: start/install
title: Installation
---

### Install via Composer

Download Splash Sylius Plugin and its dependencies to the vendor directory. You can use Composer for the automated process:
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

### Connect to your Splash Account

First, you need to create access keys for you module in our website. To do so, on Splash workspace, go to **Servers** >> **Add a Server** and note your id & encryption keys. 

![](https://splashsync.gitlab.io/Sylius-Bundle/assets/img/screenshot_2.png)

### Configure Splash Bundles

Here is the default configuration for Splash bundles:

```yml

    splash:
      id:             ThisIsSyliusWsId                # Your Splash Server ID
      key:            ThisIsSyliusWsEncryptionKey     # Your Server Secret Encryption Key

    splash_sylius_splash:
      default_channel:    FASHION_WEB                 # Select here your shop default channel

```

### Configure Splash Routes

Add Splash Bundle routes to your configuration:

```yml
    splash_ws:
        resource: "@SplashBundle/Resources/config/routing.yml"
        prefix: /ws
```

### Test & Connect your server

Once your server is created in your account, you need to declare it.

To do so, open your web browser and touch "http://my.webshop.com/ws/splash-test" url.

![](https://splashsync.gitlab.io/Sylius-Bundle/assets/img/screenshot_1.png)

### Requirements

* PHP 7.4+
* Sylius 1.6+
* An active Splash Sync User Account
