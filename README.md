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

## AssetsManager

The `AssetsManager` manager does the heavy lifting of registering and enqueuing assets for you. It works with a configuration file in YAML with the following format:

```yaml
<template-name-or-all>:
  css:
    <asset-id>: <asset-path-in-theme>
  js:
    <asset-id>: <asset-path-in-theme>
```

If used with our [Webpack configuration package](https://github.com/studiometa/webpack-config), you can also specify entrypoints and all their dependencies to be registered and enqueued.

```yaml
all:
  entries:
    - css/app.scss
    - js/app.js
```

```php
new AssetsManager(
  get_template_directory() . '/config/assets.yml',
  get_template_directory() . '/dist/assets-manifest.json',
);
```

### Parameters

- `$configuration_filepath` (`string`): path to the `config.yml` file, defaults to `config/assets.yml` in your theme.
- `$webpack_manifest_filepath` (`string`), path to the Webpack assets manifest file, defaults to `dist/assets-manifest.json` in your theme.

## Helpers

Functions to interact with WordPress behaviour.

### Plugin helper

```php
use Studiometa\WPToolkit\Helpers\PluginHelper;
// Check if a specified plugin is enable.
PluginHelper::is_plugin_enabled( 'my-plugin/my-plugin.php' );
```

## Transient Cleaner

### Usage

> **Important** Transients keys must be prefixed with transient cleaner prefix (`TransientCleaner::PREFIX`) to be tracked.

```php
use Studiometa\WPToolkit\TransientCleaner;

// 1. Set a transient with transient cleaner prefix.
if ( $my_condition ) {
  set_transient(
    TransientCleaner::PREFIX . 'transient_key',
    'example'
  );
}

// 2. Initialize transient cleaner.
$transient_cleaner = TransientCleaner::get_instance(
  array(
    'post'   => array(
      'all'           => array(
        TransientCleaner::PREFIX . 'transient_key',
      ),
      'post_type_key' => array(
        TransientCleaner::PREFIX . 'transient_key',
        TransientCleaner::PREFIX . 'transient_key_1',
      )
    ),
    'term'   => array(
      'all'                    => array(),
      'your_taxonomy_type_key' => array(),
      'category'               => array(),
    ),
    'option' => array(
      'all'             => array(),
      'option_key'      => array(),
      'blogdescription' => array(),
    ),
  )
);

// Update config if needed.
$transient_cleaner->set_config(array());

// 3. Insert/Update post/term/option to see your transients deleted based on your config.
```

## Contribute

### Run tests

#### PHPUnit

```bash
# WP-tests must be installed before run PHPUnit (required a test MySQL database).
./bin/install-wp-tests.sh [dbname] [dbuser] [dbpasswd] [dbhost] [test_version]
composer run-script phpunit
```

Tests can be run in ddev which preconfigures the WordPress environment when starting:

```bash
ddev start
ddev exec phpunit
```

To test against different PHP version, you can edit the `.ddev/config.yaml` file and change the `php_version` property.
