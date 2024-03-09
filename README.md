

# Flutter team instructions

## status
```
{{host}}/api/status
```

with
```
param: model
value: one of [video, comment]
```

<br>
<br>

## Public / private account

```
{{host}}/api/auth/profile/preferences/account [post]
```
with:
```
param: status
value: one of [0, 1]
```

# Deployment

* create domain
* create database
* create database user
* give all database privilages to user
* git clone repo
* run commands
```
composer install
```
```
cp .env.example .env
```
```
nano .env
```
```
php artisan migrate --seed
```
```
php artisan passport:install
```
```
rm -fr public/files
```
```
php artisan storage:link
```