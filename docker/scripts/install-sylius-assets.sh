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

################################################################################
# Check if Sylius Assets is Installed
if [ ! -f /home/assets.installed.lock ]; then

    echo "Install Sylius Assets"
    yarn install && yarn build
    bin/console assets:install public --no-interaction
    bin/console sylius:theme:assets:install public --no-interaction

    echo "YEP" > /home/assets.installed.lock
else
    echo "SKIP >> Sylius Assets Already Installed"
fi