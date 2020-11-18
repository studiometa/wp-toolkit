<?php

use Studiometa\WPToolkit\Builders\PostTypeBuilder;

it(
	'should register a custom post type',
	function() {
		( new PostTypeBuilder( 'product' ) )
		->set_labels( 'Product', 'Products' )
		->register();

		expect( $this->type )->toBe( 'product' );
		expect( $this->config )->toEqual(
			array(
				'public'           => true,
				'show_in_rest'     => false,
				'menu_position'    => null,
				'has_archive'      => false,
				'supports'         => array( 'title', 'editor', 'thumbnail' ),
				'can_export'       => true,
				'delete_with_user' => null,
				'labels'           => array(
					'name'                  => 'Products',
					'singular_name'         => 'Product',
					'add_new'               => 'Add New Product',
					'add_new_item'          => 'Add New Product',
					'edit_item'             => 'Edit Product',
					'new_item'              => 'New Product',
					'all_items'             => 'All Products',
					'view_item'             => 'View Product',
					'search_items'          => 'Search Products',
					'not_found'             => 'No Products',
					'not_found_in_trash'    => 'No Products found in Trash',
					'parent_item_colon'     => '',
					'menu_name'             => 'Products',
					'insert_into_item'      => 'Insert into product',
					'uploaded_to_this_item' => 'Uploaded to this product',
					'items_list'            => 'Products list',
					'items_list_navigation' => 'Products list navigation',
					'filter_items_list'     => 'Filter products list',
				),
			)
		);
	}
);
