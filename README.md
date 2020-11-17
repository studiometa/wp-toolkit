# WordPress utilities

> Boost your WordPress development with PHP utilities.

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
(new PostTypeBuilder( 'product' ))
  ->set_labels( 'Product', 'Products' )
  ->set_has_archive( true )
  ->register();
```
