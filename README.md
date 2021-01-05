Config
==========

> Config - configuration parser - somewhere between ZF1 and ZF2, sections with include ...

## Install

> Using Composer and Packagist

```sh
composer require startcode/config
```

## Usage

```php
$config = new Startcode\Config\Config();

$data = $config
    ->setCachePath(__DIR__)
    ->setSection('local')
    ->setPath('config.ini')
    ->getData();
```

## Development

### Install dependencies

    $ make install

### Run tests

    $ make test

## License

(The MIT License)
see LICENSE file for details...
