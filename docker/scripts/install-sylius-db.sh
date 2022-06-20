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

################################################################################
# Check if Sylius Database is Installed
if [ ! -f /home/db.installed.lock ]; then

    echo "Install Sylius Database"

    bin/console about

    ################################################################################
    # Check for MySQL
    until bin/console doctrine:query:sql "select 1" >/dev/null 2>&1; do
        (>&2 echo "Waiting for MySQL to be ready...")
      sleep 1
    done
    ################################################################################
    # Update Database & Load Fixtures
    bin/console doctrine:migrations:migrate --no-interaction
    bin/console sylius:fixtures:load --no-interaction

    echo "YEP" > /home/db.installed.lock
else
    echo "SKIP >> Sylius Database Already Installed"
fi
