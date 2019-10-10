# Laravel API

## What is it

It's a simple REST API based on Laravel 6. We have magazines and publishers.
One publisher can have many magazines.

## Endpoints

### Getting access token

uri: oauth/token
method: POST
header: Content-Type: application/json
body:
{
    "grant_type": "password",
    "client_id": Your client id,
    "client_secret": Your client secret,
    "username": "admin",
    "password": "admin",
    "scope": ""
}

As a response You will get access_token which You later can use with Authorization Bearer header

### List publishers

uri: api/v1/publishers/list
method: GET
headers:
    Authorization: Bearer access_token
    Accept: application/json

additional parameters You can add in query string:
order_by: "id" or "name"
per_page: int
page: int

### Search magazines

uri: api/v1/magazines/search
method: GET
headers:
    Authorization: Bearer access_token
    Accept: application/json

additional parameters You can add in query string:
publisher_id: int
name_part: string (at least three characters)
per_page: int
page: int

### Show magazine

uri: api/v1/magazines/{id}
method: GET
headers:
    Authorization: Bearer access_token
    Accept: application/json

## How to install

Start by typing in terminal:
```
composer install
php artisan passport:keys
php artisan key:generate
```
Second command will generate oauth-private.key and oauth-public.key in storage folder.
The last command will create app key in .env file. Please copy that key to .env.testing file.

Create two databases, one standard and other for testing.
Then put db credentials in .env and .env.testing files.

If You migrate tables to test database:
```
php artisan migrate --env=testing
```
It should be enough for all phpunit tests to pass. Try it.

You should also migrate to standard table, and may seed tables.
```
php artisan migrate
php artisan db:seed
```

Now You should make Oauth2 client with a password grant.
```
php artisan passport:client --password
```
We assume, that every logged in user is authorized to use API - there is no user roles.

You should also create vhost indicating to ./public and add host to /etc/hosts.
