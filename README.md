# Laravel Env Normalizer

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/Laravel-Env-Normalizer.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-env-normalizer)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/Laravel-Env-Normalizer.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-env-normalizer)
[![License](https://img.shields.io/packagist/l/romanzipp/Laravel-Env-Normalizer.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-env-normalizer)
[![GitHub Build Status](https://img.shields.io/github/workflow/status/romanzipp/Laravel-Env-Normalizer/Tests?style=flat-square)](https://github.com/romanzipp/Laravel-Env-Normalizer/actions)

Format .env files accordiaccording to your .env.example structure

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Installation

```
composer require romanzipp/laravel-env-normalizer
```

## Configuration

Copy configuration to config folder:

```
$ php artisan vendor:publish --provider="romanzipp\EnvNormalizer\Providers\EnvNormalizerServiceProvider"
```

## Usage

```shell
todo
```

## Testing

```
./vendor/bin/phpunit
```

| `.env.example` | previous `.env` | new `.env` |
| --- | --- | --- |
| <pre>BASE_URL=http://localhost <br><br># Databse<br><br>DB_HOST=127.0.0.1<br>DB_PORT=${DEFAULT_PORT}<br>DB_USER=test<br>DB_PASSWORD=<br><br># Mail<br><br>MAIL_CONNECTION=</pre> | <pre>DB_HOST=10.0.0.10<br>BASE_URL=http://example.com<br>DB_USER=prod<br>DB_PASSWORD=123456<br># Mail<br>MAIL_CONNECTION=foo<br>MAIL_FROM=mail@example.com</pre> | <pre>BASE_URL=http://example.com<br><br># Databse<br><br>DB_HOST=10.0.0.10<br>DB_USER=prod<br>DB_PASSWORD=123456<br><br># Mail<br><br>MAIL_CONNECTION=foo<br><br># Not found while normalizing<br><br>MAIL_FROM=mail@example.com</pre> |
