# Laravel Env Normalizer

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/Laravel-Env-Normalizer.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-env-normalizer)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/Laravel-Env-Normalizer.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-env-normalizer)
[![License](https://img.shields.io/packagist/l/romanzipp/Laravel-Env-Normalizer.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-env-normalizer)
[![GitHub Build Status](https://img.shields.io/github/workflow/status/romanzipp/Laravel-Env-Normalizer/Tests?style=flat-square)](https://github.com/romanzipp/Laravel-Env-Normalizer/actions)

Format .env files accordiaccording to your .env.example structure.

This package will take your existing .env.example file and apply the structure to other specified .env files.

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Installation

```
composer require romanzipp/laravel-env-normalizer
```

## Usage

```shell
php artisan env:normalize {--reference=.env.example} {--target=.env}
```

### Call help command for all options

```shell
php artisan env:normalize --help
```

### Specify reference and target file(s)

```shell
php artisan env:normalize --reference=.env.example --target=.env --target=.env.local
```

### Automatically format all other .env files

```shell
php artisan env:normalize --reference=.env.example --auto
```

### Example normalization

| `.env.example` | previous `.env` | new `.env` |
| --- | --- | --- |
| <pre>BASE_URL=http://localhost <br><br># Databse<br><br>DB_HOST=127.0.0.1<br>DB_PORT=${DEFAULT_PORT}<br>DB_USER=<br>DB_PASSWORD=<br><br># Mail<br><br>MAIL_CONNECTION=<br><br><br></pre> | <pre>DB_HOST=10.0.0.10<br>BASE_URL=http://me.com<br>DB_USER=prod<br>DB_PASSWORD=123456<br># Mail<br>MAIL_CONNECTION=foo<br>MAIL_FROM=mail@me.com<br><br><br><br><br><br><br><br><br></pre> | <pre>BASE_URL=http://me.com <br><br># Databse<br><br>DB_HOST=10.0.0.10<br>DB_USER=prod<br>DB_PASSWORD=123456<br><br># Mail<br><br>MAIL_CONNECTION=foo<br><br># Not found while normalizing<br><br>MAIL_FROM=mail@me.com</pre> |

## Testing

```
./vendor/bin/phpunit
```
