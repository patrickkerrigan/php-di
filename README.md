[![Build Status](https://img.shields.io/github/workflow/status/patrickkerrigan/php-di/Tests.svg?style=flat-square)](https://github.com/patrickkerrigan/php-di/actions/workflows/tests.yml) [![Code Climate](https://img.shields.io/codeclimate/maintainability/patrickkerrigan/php-di.svg?style=flat-square)](https://codeclimate.com/github/patrickkerrigan/php-di) [![Coverage](https://img.shields.io/codeclimate/c/patrickkerrigan/php-di.svg?style=flat-square)](https://codeclimate.com/github/patrickkerrigan/php-di) [![PHP 7.1](https://img.shields.io/badge/php-7.1-blue.svg?style=flat-square)](http://php.net/) [![Packagist](https://img.shields.io/packagist/v/pkerrigan/di.svg?style=flat-square)](https://packagist.org/packages/pkerrigan/di)

# pkerrigan/di
A simple, lightweight PHP 7.1 dependency injector.

## Why?
This was for fun, and doesn't really compare to the features offered by the popular dependency injectors. If you just need to inject objects then you may find this useful.

## Usage

```php
$injector = Injector::getInstance();
 
$injector->addClassResolver(new ArrayMapClassResolver([
    Interface::class => ConcreteImplementation::class
]));
 
$instance = $injector->get(Interface::class);
```

By default, all objects are treated as singletons (that is, the same object will be returned for successive calls to ```get()```). If you wish an object to be constructed again for every call of get (or injection) then you can explicitly define it as a ```Prototype``` like so:

```php
$injector->addClassResolver(new ArrayMapClassResolver([
    Interface::class => new Prototype(ConcreteImplementation::class)
]));
```
