# SUITERUS TECHNOLOGIES INC.`s BACKEND TEMPLATE

This git repo is exclusive for the employees of Suiterus Technologies INC. only.

## INSTALLATION
```
git clone <repo_url> <folder_name_here>
cd <folder_name_here>
composer install
npm install

# copy .env.example file
cp .env.example .env
# or
copy .env.example .env

# edit .env file
APP_URL="localhost:8000" #you can also put here your api domain/url.
CLIENT_URL="localhost:3000"

DB_DATABASE=<your_db_name>
DB_USERNAME=<your_db_username>
DB_PASSWORD=<your_db_password>

# run these necessary commands
php artisan key:generate
php artisan jwt:secret
php artisan migrate
npm run build
```

## Supported in valet users only
If you are using other webserver setup aside from valet. Please contact roldhan.dasalla@suiterus.com for assistance.

## Valet commands
Create local domain & virtual host with SSL. 
Change the `<project-name>` with your desired value.
```
valet link <project-name>
valet secure <project-name>
```
Add your local domain to `C:\Windows\System32\Drivers\etc\Hosts` or in `AcylicHost UI`.
```
127.0.0.1   <project-name>.test
```

## USAGE
Visit on your browser the local domain you have created.
Change the `<project-name>` with your desired value.
```
# for ssl
https://<project-name>.test

# for non-ssl
http://<project-name>.test
```
<br><br>
<p>
    Created by Roldhan Dasalla
</p>
<p>
    Dependecy template : <a href="https://github.com/cretueusebiu/laravel-nuxt" target="_blank">https://github.com/cretueusebiu/laravel-nuxt</a>
</p>
