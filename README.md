# Compari trusted shop

This package provides basic support for Compari or Arukereso trusted shop.

## Installation

```shell
$ composer require ssanko/compari-trusted-shop
```

## Usage

Usage for Compari trusted shop:

```php
try {
    $client = (new \Ssanko\Compari\TrustedShop('<apiKey>', new \Ssanko\Compari\Config\CompariConfig))
        ->setEmail('somebody@example.com')
        ->addProduct('Product name 1', 'P123456');

    echo $client->createTrustedCode();
} catch (Exception $exception) {
  die($exception->getMessage());
}
```

Usage for Arukereso trusted shop:

```php
try {
    $client = (new \Ssanko\Compari\TrustedShop('<apiKey>', new \Ssanko\Compari\Config\ArukeresoConfig))
        ->setEmail('somebody@example.com')
        ->addProduct('Product name 1', 'P123456');

    echo $client->createTrustedCode();
} catch (Exception $exception) {
  die($exception->getMessage());
}
```
