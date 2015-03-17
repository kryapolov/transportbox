<!-- vim: set tw=79 sw=4 ts=4 et ft=markdown : -->
# TransportBox
## Implement simpte transport conteiner for PHP

* Version: **0.0.1**

A simple transport conteiner package for PHP 5.3/5.4.

#### Public API

The new vendor namespace is TransportBox. This namespace begins in the `/lib`
directory.

## Installation

The library is PSR-0 compatible, with a vendor name of **TransportBox**. An
SplClassLoader is bundled for convenience.

## Usage

 An example call full public api TransportBox based on file container -
 see example.php in the examples directory.

## Authors

The original maintainer and author was
[@kryapolov](https://github.com/kryapolov). The code is licensed under the WTFPL, a free software compatible
license.

## Examples

- exec test: phpunit -c build/phpunit.xml --coverage-text
- exec generate autoload: phpab --output lib/TransportBox/autoload.php lib/TransportBox
- exec example: php examples/example.php
- For detail see example.php in the examples directory.