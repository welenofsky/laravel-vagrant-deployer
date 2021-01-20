#!/bin/bash
if ! command -v composer 2> /dev/null; then
    sudo mkdir /usr/share/adminer && sudo wget "http://www.adminer.org/latest.php" -O /usr/share/adminer/latest.php && sudo ln -s /usr/share/adminer/latest.php /usr/share/adminer/adminer.php && echo "Alias /adminer.php /usr/share/adminer/adminer.php" | sudo tee /etc/apache2/conf-available/adminer.conf && sudo a2enconf adminer.conf
fi
