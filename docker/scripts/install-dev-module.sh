#!/bin/sh
################################################################################
#
#  This file is part of SplashSync Project.
#
#  Copyright (C) Splash Sync <www.splashsync.com>
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
#
#  For the full copyright and license information, please view the LICENSE
#  file that was distributed with this source code.
#
#  @author Bernard Paquier <contact@splashsync.com>
#
################################################################################

set -e
cd /var/www/html

if [ ! -f /home/module.installed.lock ]; then

    echo "Install Splash DEV Module via Composer"
    composer config extra.symfony.allow-contrib true
    composer config repositories.splash '{ "type": "path", "url": "/builds/SplashSync/Sylius-Bundle", "options": { "symlink": true, "versions": { "splash/sylius-splash-plugin": "dev-local" }}}'
    composer config minimum-stability dev
    COMPOSER_MEMORY_LIMIT=-1 composer require splash/sylius-splash-plugin:dev-local --no-scripts --no-progress --no-suggest

    echo "YEP" > /home/module.installed.lock
else
    echo "SKIP >> Splash DEV Module Already Installed"
fi

echo "Install Splash DEV Module Config Files"
cp -Rf /builds/SplashSync/Sylius-Bundle/ci/config /var/www/html
cp -Rf /builds/SplashSync/Sylius-Bundle/config /var/www/html
cp -Rf /builds/SplashSync/Sylius-Bundle/phpunit.xml.dist /var/www/html/phpunit.xml.dist

echo "LIST Splash Components Installed"
composer info | grep "splash"