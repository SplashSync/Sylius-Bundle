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
# Check if Yarn is Installed
sh /builds/SplashSync/Sylius-Bundle/docker/scripts/install-yarn.sh
################################################################################
# Check if Sylius Core is Installed
sh /builds/SplashSync/Sylius-Bundle/docker/scripts/install-sylius-core.sh
################################################################################
# Check if Sylius Assets are Installed
sh /builds/SplashSync/Sylius-Bundle/docker/scripts/install-sylius-assets.sh
################################################################################
# Check if Sylius Database is Installed
sh /builds/SplashSync/Sylius-Bundle/docker/scripts/install-sylius-db.sh
################################################################################
# Check if Splash Dev Module is Installed
sh /builds/SplashSync/Sylius-Bundle/docker/scripts/install-dev-module.sh
################################################################################
echo "Enable Mod Rewrite"
a2enmod rewrite

echo "Serving App..."
exec apache2-foreground
