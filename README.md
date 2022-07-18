# Site Online Store shop (learning project)

## How install app for Ubuntu and deploying

After installation Ubuntu, need run

1. sudo apt upgrade
</br>
2. sudo apt update
</br>
3. sudo apt install libzip-dev libpng-dev libpq-dev libxslt1.1 libxslt1-dev libnss3-tools mc
</br>
4. sudo apt install software-properties-common
</br>

*This is need for php 7.4*

5. sudo apt update
</br>

*Repository with packages php*

6. sudo add-apt-repository ppa:ondrej/php
</br>

### Install php 7.4

1. sudo apt install php7.4 php7.4-fpm php7.4-zip php7.4-gd php7.4-pgsql php7.4-xsl php7.4-curl php7.4-mbstring php7.4-intl
    enter: yes
</br>


### Install composer
1. cd ~/
</br>

2. wget https://getcomposer.org/download/2.3.10/composer.phar
</br>

3. sudo mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer
</br>

3. composer --help
</br>


### Install NodeJS
#### Using Ubuntu
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
</br>
sudo apt-get install -y nodejs
</br>


#### Using Debian, as root
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
</br>
apt-get install -y nodejs
</br>

**OR**

### Install NodeJS, NPM
sudo apt update
</br>
sudo apt install nodejs npm
</br>


### Install Symfony CLI
</br>
curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
</br>
sudo apt install symfony-cli
</br>
~~sudo mv /home/developer/.symfony/bin/symfony /usr/local/bin/symfony~~
</br>


### Create folder for project
sudo mkdir -p /var/www/online_store
</br>
sudo chown developer:developer /var/www/online_store
</br>
cd /var/www/
</br>

### Install ssh

cd ~/
</br>

ssh-keygen -o

</br>
*Copy key*

cat ~/.ssh/id_rsa.pub
</br>
*Added key into a need site repository*
</br>

### Clone github project

*Into /var/www/online_store*

git clone git@github.com:phpcount/online-store.git online_store/
</br>

</br>


### Install Nginx

1. sudo apt install nginx
enter: yes
</br>
2. sudo systemctl enable nginx
</br>

3. sudo chown -R $USER:$USER /var/www/online_store
</br>

4. sudo chmod -R 775 /var/www/online_store
</br>

5. sudo nano /etc/nginx/sites-available/online_store

[Symfony Nginx](https://symfony.com/doc/current/setup/web_server_configuration.html)

  *Paste and save that code (press Ctrl + X and Ctrl + Y)*
<code>

server {
    # dns or ip address
    server_name domain.tld www.domain.tld;
    root /var/www/online_store/public;

    location / {
        # try to serve file directly, fallback to index.php
        try_files $uri /index.php$is_args$args;
    }

    # optionally disable falling back to PHP script for the asset directories;
    # nginx will return a 404 error when files are not found instead of passing the
    # request to Symfony (improves performance but Symfony's 404 page is not displayed)
    # location /bundles {
    #     try_files $uri =404;
    # }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;

        # optionally set the value of the environment variables used in the application
        # fastcgi_param APP_ENV prod;
        # fastcgi_param APP_SECRET <app-secret-id>;
        # fastcgi_param DATABASE_URL "mysql://db_user:db_pass@host:3306/db_name";

        # When you are using symlinks to link the document root to the
        # current version of your application, you should pass the real
        # application path instead of the path to the symlink to PHP
        # FPM.
        # Otherwise, PHP's OPcache may not properly detect changes to
        # your PHP files (see https://github.com/zendtech/ZendOptimizerPlus/issues/126
        # for more information).
        # Caveat: When PHP-FPM is hosted on a different machine from nginx
        #         $realpath_root may not resolve as you expect! In this case try using
        #         $document_root instead.
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        # Prevents URIs that include the front controller. This will 404:
        # http://domain.tld/index.php/some-path
        # Remove the internal directive to allow URIs like this
        internal;
    }

    # return 404 for all other php files not matching the front controller
    # this prevents access to other php files you don't want to be accessible.
    location ~ \.php$ {
        return 404;
    }

    error_log /var/log/nginx/online_store_error.log;
    access_log /var/log/nginx/online_store_access.log;
}
</code>

</br>
6. sudo ln -s /etc/nginx/sites-available/online_store /etc/nginx/sites-enabled/online_store

</br>

  *Check status Nginx*

7. sudo nginx -t

</br>

8. sudo systemctl restart nginx

</br>

### Install Composer

*For production, need: --no-dev --optimize-autoloader*

1. sudo composer install --no-dev --optimize-autoloader

    or without sudo

    **For fix**
    </br>

    1. cp .env .env.local
    </br>

    2. mc

    *select nano*
    </br>

    <code>
    Into file .env.local need a change: with APP_ENV=dev to APP_ENV=prod
    </code>
    </br>

    After repeat installation composer

</br>

*Clear cache at prod*

2. php bin/console cache:clear --env=prod --no-debug

*Into .env.local added: APP_DEBUG=1*
</br>

[Using ACL on a System that Supports setfacl (Linux/BSD)](https://symfony.com/doc/current/setup/file_permissions.html)

*Get the necessary rights, for example, for logging...*

<code>
HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)

sudo setfacl -dR -m u:"\$HTTPDUSER":rwX -m u:$(whoami):rwX var

sudo setfacl -R -m u:"\$HTTPDUSER":rwX -m u:$(whoami):rwX var
</code>

*After edit SITE_BASE_HOST into .env.local and url for Database*

php bin/console cache:clear


### Database deploying

Create Database
1. php bin/console doctrine:schema:create
</br>
1. php bin/console doctrine:schema:create
</br>

</br>


### Npm install for manifest.json
1. npm install
</br>

*there may be an error, then try again*

sudo chown -R 1001:1002 /home/developer/.npm
</br>

2. npm run build
