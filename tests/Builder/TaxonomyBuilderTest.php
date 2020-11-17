<?php

use Studiometa\WP\Builder\TaxonomyBuilder;

it(
	'should register a custom taxonomy',
	function() {
		( new TaxonomyBuilder( 'category' ) )
		->set_post_types( 'post' )
		->set_labels( 'Category', 'Categories' )
		->register();

		expect( $this->type )->toBe( 'category' );
		expect( $this->post_types )->toBe( 'post' );
		expect( $this->config )->toEqual(
			array(
				'query_var'         => 1,
				'show_ui'           => 1,
				'show_admin_column' => 1,
				'show_in_nav_menus' => 1,
				'show_tagcloud'     => 1,
				'labels'            => array(
					'name'                  => 'Categories',
					'singular_name'         => 'Category',
					'add_new_item'          => 'Add New Category',
					'all_items'             => 'All Categories',
					'choose_from_most_used' => 'Most used',
					'edit_item'             => 'Edit Category',
					'items_list'            => 'Categories list',
					'items_list_navigation' => 'Categories list navigation',
					'menu_name'             => 'Categories',
					'new_item_name'         => 'New Category',
					'no_terms'              => 'No Category',
					'parent_item'           => 'Category parent',
					'parent_item_colon'     => '',
					'popular_items'         => 'Popular Category',
					'search_items'          => 'Search Categories',
					'update_item'           => 'Update the Category',
					'view_item'             => 'View Category',
				),
			)
		);
	}
);
