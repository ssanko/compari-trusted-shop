# Compari trusted shop
This package provides basic support for Compari trusted shop.

## Installation

```shell
$ composer require ssanko/compari-trusted-shop
```

## Usage
Baisc usage:

```php
try {
    $client = (new \Ssanko\Compari\TrustedShop('<apiKey>'))
        ->setEmail('somebody@example.com')
        ->addProduct('Product name 1', 'P123456');

    echo $client->createTrustedCode();
} catch (Exception $exception) {
  die($exception->getMessage());
}
```
