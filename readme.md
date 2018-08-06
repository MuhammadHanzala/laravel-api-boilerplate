# Laravel Api

This is a boilerplate server

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

Follow this [Guide](https://gist.github.com/hootlex/da59b91c628a6688ceb1#file-laravellocal-md) for prerequisites.


### Installing

- Open the console and cd your project root directory
- Run `composer install`
- Rename `.env.example` file to `.env`inside your project root and fill the database information.
- Enter credentials of database and mailtrap in `env` file.
- Run `php artisan key:generate`
- Run `php artisan migrate`
- Run `php artisan passport:install`
- Run `php artisan db:seed` to run seeders, if any.
- Run `php artisan serve`

## Built With

* [Laravel](https://laravel.com/docs/5.6) - The framework used
* [Composer](https://getcomposer.org/) - Dependency Manager

## Contributors

* **Muhammad Hanzala** - [Github](https://github.com/muhammadhanzala)
