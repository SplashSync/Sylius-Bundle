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


################################################################################
# Check if Sylius Database is Installed
if [ ! -f /home/core.installed.lock ]; then
    ################################################################################
    # Install Sylius Standard Project
    echo "Install Sylius $SYLIUS_VERSION"
    composer create-project sylius/sylius-standard /var/www/html $SYLIUS_VERSION

    mkdir -p var/cache var/log public/media
    chmod -Rf 777 var public/media
    composer update --prefer-dist --no-interaction

    echo "YEP" > /home/core.installed.lock
else
    echo "SKIP >> Sylius Core Already Installed"
fi