<?php

function __( $string, $domain ) {
	return $string;
}

function register_taxonomy( $type, $post_types, $config ) {
	test()->type       = $type;
	test()->post_types = $post_types;
	test()->config     = $config;
}

function register_post_type( $type, $config ) {
	test()->type   = $type;
	test()->config = $config;
}
