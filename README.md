# WordPress toolkit

[![Packagist Version](https://img.shields.io/github/v/release/studiometa/wp-toolkit?include_prereleases&label=packagist&style=flat-square)](https://packagist.org/packages/studiometa/wp)
[![License MIT](https://img.shields.io/packagist/l/studiometa/wp-toolkit?style=flat-square)](https://packagist.org/packages/studiometa/wp)
[![Codecov](https://img.shields.io/codecov/c/github/studiometa/wp-toolkit?style=flat-square)](https://codecov.io/gh/studiometa/wp-toolkit/)

> A PHP toolkit to boost your WordPress development! 🚀

## Installation

Install the package via Composer: 

```bash
composer require studiometa/wp-toolkit
```

## Usage

```php
use Studiometa\WPToolkit\Assets;
use Studiometa\WPToolkit\Cleanup;
use Studiometa\WPToolkit\Builders\PostTypeBuilder;

// Load assets from `assets.yaml` configuration
new Assets( get_template_directory() );

// Clean WordPress
new Cleanup();

// Create Custom Post Type
$cpt = new PostTypeBuilder( 'product' );
$cpt->set_labels( 'Product', 'Products' )
  ->set_has_archive( true )
  ->register();

// Create Custom Taxonomy
$tax = new TaxonomyBuilder( 'product-cat' );
$tax->set_post_types( 'product' )
  ->set_labels( 'Product Category', 'Product Categories' )
  ->register();
```
