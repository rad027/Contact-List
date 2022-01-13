# RAD`s BACKEND TEMPLATE

This git repo is exclusive for RAD Projects only. This is a modified start tempalte, credits has been given at the end of this document.

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
APP_URL="https://<project-name>.test" #you can also put here your api domain/url.

DB_DATABASE=<your_db_name>
DB_USERNAME=<your_db_username>
DB_PASSWORD=<your_db_password>

# run these necessary commands
php artisan key:generate
php artisan jwt:secret
php artisan migrate:fresh --seed
npm run build
```

## Supported in valet users only
If you are using other webserver setup aside from valet. Please contact roldhandasalla27@gmail.com for assistance.

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
