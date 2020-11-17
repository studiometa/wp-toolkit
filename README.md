# WordPress toolkit

[![Packagist Version](https://img.shields.io/github/v/release/studiometa/wp?include_prereleases&label=packagist&style=flat-square)](https://packagist.org/packages/studiometa/wp)
[![License MIT](https://img.shields.io/packagist/l/studiometa/wp?style=flat-square)](https://packagist.org/packages/studiometa/wp)

> A PHP toolkit to boost your WordPress development! 🚀

## Installation

Install the package via Composer: 

```bash
composer require studiometa/wp
```

## Usage

```php
use Studiometa\WP\Assets;
use Studiometa\WP\Cleanup;
use Studiometa\WP\Builders\PostTypeBuilder;

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
