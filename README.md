# UNI CHAT

# Installation 

* **clone**
* composer install 
* npm i
* copy .env to env.local
* set database: DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db
* php bin/console doctrine:database:create
* php bin/console doctrine:schema:update --dump-sql --force

# Pull procedure

### Just

* run ```sh pull.sh```

### OR
* **pull**
* composer install 
* npm i
* php bin/console doctrine:schema:update --dump-sql --force

# Dev

* For dev server run: 
    * cd public
    * php -S 127.0.0.1:8080
* For style watcher run: npm run watch
* Database stored in /var/data.db

