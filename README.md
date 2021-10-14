# Laravel Env Normalizer

[![Latest Stable Version](https://img.shields.io/packagist/v/romanzipp/Laravel-Env-Normalizer.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-env-normalizer)
[![Total Downloads](https://img.shields.io/packagist/dt/romanzipp/Laravel-Env-Normalizer.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-env-normalizer)
[![License](https://img.shields.io/packagist/l/romanzipp/Laravel-Env-Normalizer.svg?style=flat-square)](https://packagist.org/packages/romanzipp/laravel-env-normalizer)
[![GitHub Build Status](https://img.shields.io/github/workflow/status/romanzipp/Laravel-Env-Normalizer/Tests?style=flat-square)](https://github.com/romanzipp/Laravel-Env-Normalizer/actions)

### ⚠️ **WORK IN PROGRESS** ⚠️

Format `.env` files according to your `.env.example` structure to keep track of used and unused variables.

#### Why?

I like to keep a clear overview of all available environment variables by adding some default values to my version controled `.env.example` file.
This packages helps with structuring your example files.

## Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Testing](#testing)

## Installation

```shell
composer require romanzipp/laravel-env-normalizer --dev
```

## Usage

```shell
php artisan env:normalize
```

#### List all available options

```shell
php artisan env:normalize --help
```

#### Specify reference and target file(s)

Reference and target options are optional. If not specified the command will only look for a `.env.example` (as reference) and `.env` file (as target).

```shell
php artisan env:normalize --reference=.env.example --target=.env --target=.env.local
```

#### Automatically format all other .env files

This option will discover any other `.env.*` files located in the base path and add them to the target list.

```shell
php artisan env:normalize --auto
```

#### Create backup files

This will create a `{name}.bak` backup file for each modified target file.

```shell
php artisan env:normalize --backup
```

#### Dry run

Log the expected output to the console instead of writing it to the file.

```shell
php artisan env:normalize --dry
```

### Example normalization

| `.env.example` | previous `.env` | new `.env` |
| --- | --- | --- |
| <pre>BASE_URL=http://localhost <br><br># Database<br><br>DB_HOST=127.0.0.1<br>DB_PORT=${DEFAULT_PORT}<br>DB_USER=<br>DB_PASSWORD=<br><br># Mail<br><br>MAIL_CONNECTION=<br><br><br></pre> | <pre>DB_HOST=10.0.0.10<br>BASE_URL=http://me.com<br>DB_USER=prod<br>DB_PASSWORD=123456<br># Mail<br>MAIL_CONNECTION=foo<br>MAIL_FROM=mail@me.com<br><br><br><br><br><br><br><br><br></pre> | <pre>BASE_URL=http://me.com <br><br># Database<br><br>DB_HOST=10.0.0.10<br>DB_USER=prod<br>DB_PASSWORD=123456<br><br># Mail<br><br>MAIL_CONNECTION=foo<br><br># Additional<br><br>MAIL_FROM=mail@me.com</pre> |

- The base structure for all target `.env` files will be taken from the reference `.env.example` file
- Values will be replaced with the existing content
- Unused (not overwritten) example variables will not be added
- Additional variables from the `.env` file will be appended to the bottom so you can later add them to your version controled example file

## Features

- [ ] Detect similar variables and position them below existing ones (place `MAIL_FROM` below `MAIL_CONNECTION` instead of appendin it to the end)

## Testing

```
./vendor/bin/phpunit
```
