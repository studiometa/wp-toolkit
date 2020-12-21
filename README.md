# WordPress toolkit

[![Packagist Version](https://img.shields.io/github/v/release/studiometa/wp-toolkit?include_prereleases&label=packagist&style=flat-square)](https://packagist.org/packages/studiometa/wp-toolkit)
[![License MIT](https://img.shields.io/packagist/l/studiometa/wp-toolkit?style=flat-square)](https://packagist.org/packages/studiometa/wp-toolkit)
[![Codecov](https://img.shields.io/codecov/c/github/studiometa/wp-toolkit?style=flat-square)](https://codecov.io/gh/studiometa/wp-toolkit/)

> A PHP toolkit to boost your WordPress development! 🚀

## Installation

Install the package via Composer: 

```bash
composer require studiometa/wp-toolkit
```

**Requirements**

- PHP >=7.3

## Usage

```php
// Create Custom Post Type
use Studiometa\WPToolkit\Builders\PostTypeBuilder;

$cpt = new PostTypeBuilder( 'product' );
$cpt->set_labels( 'Product', 'Products' )
  ->set_has_archive( true )
  ->register();

// Create Custom Taxonomy
use Studiometa\WPToolkit\Builders\TaxonomyBuilder;

$tax = new TaxonomyBuilder( 'product-cat' );
$tax->set_post_types( 'product' )
  ->set_labels( 'Product Category', 'Product Categories' )
  ->register();

// Create a manager
use Studiometa\WPToolkit\Managers\ManagerInterface;

class CustomManager implements ManagerInterface {
  run() {
    add_action( 'init', array( $this, 'some_action' ) );
  }

  some_action() {
    // do something on init
  }
}

// Init all managers
use Studiometa\WPToolkit\Managers\ManagerFactory;
use Studiometa\WPToolkit\Managers\AssetsManager;
use Studiometa\WPToolkit\Managers\CleanupManager;

ManagerFactory::init(
  array(
    new AssetsManager(),
    new CleanupManager(),
    new CustomManager()
  )
);
```
