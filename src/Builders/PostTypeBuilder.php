<?php
/**
 * Utilities to configure custom post types.
 *
 * @package    studiometa/wp
 * @author     Lucas Simeon <lucas.s@studiometa.fr>
 * @copyright  2020 Studio Meta
 * @license    https://opensource.org/licenses/MIT
 * @since      1.0.0
 * @version    1.0.0
 */

namespace Studiometa\WP\Builders;

/**
 * Build a custom post type.
 */
class PostTypeBuilder extends Builder {
	/**
	 * The post type configuration.
	 *
	 * @var array
	 */
	public $default_config = array(
		'public'           => true,
		'show_in_rest'     => false,
		'menu_position'    => null,
		'has_archive'      => false,
		'supports'         => array( 'title', 'editor', 'thumbnail' ),
		'can_export'       => true,
		'delete_with_user' => null,
	);

	/**
	 * The register method name.
	 *
	 * @var string
	 */
	public $register_method = 'register_post_type';

	/**
	 * The register method args.
	 *
	 * @var array
	 */
	public $register_method_args = array( 'type', 'config' );

	/**
	 * Name of the post type shown in the menu. Usually plural. Default is value of $labels['name'].
	 *
	 * @param string $label The post type label.
	 */
	public function set_label( string $label ):self {
		return $this->set_config( 'label', $label );
	}

	/**
	 * An array of labels for this post type. If not set, post labels are inherited for non-hierarchical types and page
	 * labels for hierarchical ones.
	 *
	 * @param string $singular The singular label.
	 * @param string $plural   The plural label.
	 */
	public function set_labels( string $singular, string $plural ):self {
		return $this->set_config( 'labels', $this->generate_labels( $singular, $plural ) );
	}

	/**
	 * Generate labels.
	 *
	 * @param  string $singular Singular name.
	 * @param  string $plural   Plural name.
	 * @return array            Labels.
	 */
	private function generate_labels( string $singular, string $plural ):array {
		// phpcs:disable WordPress.WP.I18n
		$labels = array(
			'name'                  => $plural,
			'singular_name'         => $singular,
			'add_new'               => sprintf( __( 'Add New %s', 'studiometa' ), $singular ),
			'add_new_item'          => sprintf( __( 'Add New %s', 'studiometa' ), $singular ),
			'edit_item'             => sprintf( __( 'Edit %s', 'studiometa' ), $singular ),
			'new_item'              => sprintf( __( 'New %s', 'studiometa' ), $singular ),
			'all_items'             => sprintf( __( 'All %s', 'studiometa' ), $plural ),
			'view_item'             => sprintf( __( 'View %s', 'studiometa' ), $singular ),
			'search_items'          => sprintf( __( 'Search %s', 'studiometa' ), $plural ),
			'not_found'             => sprintf( __( 'No %s', 'studiometa' ), $plural ),
			'not_found_in_trash'    => sprintf( __( 'No %s found in Trash', 'studiometa' ), $plural ),
			'parent_item_colon'     => isset( $this->get_config()['hierarchical'] ) && $this->get_config()['hierarchical'] ? sprintf( __( 'Parent %s:', 'studiometa' ), $singular ) : null,
			'menu_name'             => $plural,
			'insert_into_item'      => sprintf( __( 'Insert into %s', 'studiometa' ), strtolower( $singular ) ),
			'uploaded_to_this_item' => sprintf( __( 'Uploaded to this %s', 'studiometa' ), strtolower( $singular ) ),
			'items_list'            => sprintf( __( '%s list', 'studiometa' ), $plural ),
			'items_list_navigation' => sprintf( __( '%s list navigation', 'studiometa' ), $plural ),
			'filter_items_list'     => sprintf( __( 'Filter %s list', 'studiometa' ), strtolower( $plural ) ),
		);
		// phpcs:enable

		return $labels;
	}

	/**
	 * A short descriptive summary of what the post type is.
	 *
	 * @param string $description The description.
	 * @return PostTypeBuilder
	 */
	public function set_description( string $description ) {
		return $this->set_config( 'description', $description );
	}

	/**
	 * Whether a post type is intended for use publicly either via the admin interface or by front-end users. While the
	 * default settings of $exclude_from_search, $publicly_queryable, $show_ui, and $show_in_nav_menus are inherited from
	 * public, each does not rely on this relationship and controls a very specific intention. Default false.
	 *
	 * @param bool $is_public Is it public or not.
	 * @return PostTypeBuilder
	 */
	public function set_public( bool $is_public ) {
		return $this->set_config( 'public', $is_public );
	}

	/**
	 * Whether the post type is hierarchical (e.g. page). Default false.
	 *
	 * @param bool $is_hierarchical Is it hierarchical or not.
	 * @return PostTypeBuilder
	 */
	public function set_hierarchical( bool $is_hierarchical ) {
		return $this->set_config( 'hierarchical', $is_hierarchical );
	}

	/**
	 * Whether to exclude posts with this post type from front end search results.
	 * Default is the opposite value of $public.
	 *
	 * @param bool $exclude_from_search Exclude this CPT from search or not.
	 * @return PostTypeBuilder
	 */
	public function set_exclude_from_search( bool $exclude_from_search ) {
		return $this->set_config( 'exclude_from_search', $exclude_from_search );
	}

	/**
	 * Whether queries can be performed on the front end for the post type as part of parse_request().
	 * Endpoints would include:
	 *
	 * - ?post_type={post_type_key}
	 * - ?{post_type_key}={single_post_slug}
	 * - ?{post_type_query_var}={single_post_slug}
	 *
	 * If not set, the default is inherited from $public.
	 *
	 * @param bool $is_publicly_queryable Is it publicly queryable or not.
	 * @return PostTypeBuilder
	 */
	public function set_publicly_queryable( bool $is_publicly_queryable ) {
		return $this->set_config( 'publicly_queryable', $is_publicly_queryable );
	}

	/**
	 * Whether to generate and allow a UI for managing this post type in the admin. Default is value of $public.
	 *
	 * @param bool $show_ui Show this CPT in the administration or not.
	 * @return PostTypeBuilder UI?
	 */
	public function set_show_ui( bool $show_ui ) {
		return $this->set_config( 'show_ui', $show_ui );
	}

	/**
	 * Where to show the post type in the admin menu. To work, $show_ui must be true. If true, the post type is shown in
	 * its own top level menu. If false, no menu is shown. If a string of an existing top level menu (eg. 'tools.php' or
	 * 'edit.php?post_type=page'), the post type will be placed as a sub-menu of that. Default is value of $show_ui.
	 *
	 * @param bool|string $show_in_menu Show this CPT in menu.
	 * @return PostTypeBuilder in menu?
	 */
	public function set_show_in_menu( $show_in_menu ) {
		return $this->set_config( 'show_in_menu', $show_in_menu );
	}

	/**
	 * Makes this post type available for selection in navigation menus. Default is value of $public.
	 *
	 * @param bool $show_in_nav_menus Is selectable in navigation menu or not.
	 * @return PostTypeBuilder
	 */
	public function set_show_in_nav_menus( bool $show_in_nav_menus ) {
		return $this->set_config( 'show_in_nav_menus', $show_in_nav_menus );
	}

	/**
	 * Makes this post type available via the admin bar. Default is value of $show_in_menu.
	 *
	 * @param bool $show_in_admin_bar Show in admin bar or not.
	 * @return PostTypeBuilder
	 */
	public function set_show_in_admin_bar( bool $show_in_admin_bar ) {
		return $this->set_config( 'show_in_admin_bar', $show_in_admin_bar );
	}
	/**
	 * Whether to include the post type in the REST API. Set this to true for the post type to be available in the block editor.
	 *
	 * @param bool $show_in_rest Show in rest or not.
	 * @return PostTypeBuilder
	 */
	public function set_show_in_rest( bool $show_in_rest ) {
		return $this->set_config( 'show_in_rest', $show_in_rest );
	}


	/**
	 * To change the base url of REST API route. Default is $post_type.
	 *
	 * @param string $rest_base The base url of the REST API route.
	 * @return PostTypeBuilder
	 */
	public function set_rest_base( string $rest_base ) {
		return $this->set_config( 'rest_base', $rest_base );
	}

	/**
	 * REST API Controller class name. Default is 'WP_REST_Posts_Controller'.
	 *
	 * @param string $rest_controller_class The REST API Controller class name.
	 * @return PostTypeBuilder
	 */
	public function set_rest_controller_class( string $rest_controller_class ) {
		return $this->set_config( 'rest_controller_class', $rest_controller_class );
	}

	/**
	 * The position in the menu order the post type should appear. To work, $show_in_menu must be true.
	 * Default null (at the bottom).
	 *
	 * @param int $menu_position The menu position.
	 * @return PostTypeBuilder
	 */
	public function set_menu_position( int $menu_position ) {
		return $this->set_config( 'menu_position', $menu_position );
	}

	/**
	 * The url to the icon to be used for this menu. Pass a base64-encoded SVG using a data URI, which will be colored to
	 * match the color scheme -- this should begin with 'data:image/svg+xml;base64,'. Pass the name of a Dashicons helper
	 * class to use a font icon, e.g. 'dashicons-chart-pie'. Pass 'none' to leave div.wp-menu-image empty so an icon can
	 * be added via CSS. Defaults to use the posts icon.
	 *
	 * @param string $menu_icon The menu icon.
	 * @return PostTypeBuilder
	 */
	public function set_menu_icon( string $menu_icon ) {
		return $this->set_config( 'menu_icon', $menu_icon );
	}

	/**
	 * The string to use to build the read, edit & delete capabilities. May be passed as an array to allow for alternative
	 * plurals when using this argument as a base to construct the capabilities, e.g. array('story', 'stories').
	 * Default 'post'.
	 *
	 * @param string $capability_type The capability type of the CPT.
	 * @return PostTypeBuilder
	 */
	public function set_capability_type( string $capability_type ) {
		return $this->set_config( 'capability_type', $capability_type );
	}

	/**
	 * Array of capabilities for this post type. $capability_type is used as a base to construct capabilities by default.
	 * See get_post_type_capabilities().
	 *
	 * @param array $capabilities List of capabilities.
	 * @return PostTypeBuilder
	 */
	public function set_capabilities( array $capabilities ) {
		return $this->set_config( 'capabilities', $capabilities );
	}

	/**
	 * Core feature(s) the post type supports. Serves as an alias for calling add_post_type_support() directly. Core
	 * features include 'title', 'editor', 'comments', 'revisions', 'trackbacks', 'author', 'excerpt', 'page-attributes',
	 * 'thumbnail', 'custom-fields', and 'post-formats'. Additionally, the 'revisions' feature dictates whether the post
	 * type will store revisions, and the 'comments' feature dictates whether the comments count will show on the edit
	 * screen. A feature can also be specified as an array of arguments to provide additional information about supporting
	 * that feature. Example: array( 'my_feature', array( 'field' => 'value' ) ). Default is an array containing 'title'
	 * and 'editor'.
	 *
	 * @param array $supports List of features this CPT should support.
	 * @return PostTypeBuilder
	 */
	public function set_supports( array $supports ) {
		return $this->set_config( 'supports', $supports );
	}

	/**
	 * An array of taxonomy identifiers that will be registered for the post type. Taxonomies can be registered later with
	 * register_taxonomy() or register_taxonomy_for_object_type().
	 *
	 * @param array $taxonomies List of taxonomy identifiers.
	 * @return PostTypeBuilder
	 */
	public function set_taxonomies( array $taxonomies ) {
		return $this->set_config( 'taxonomies', $taxonomies );
	}

	/**
	 * Whether there should be post type archives, or if a string, the archive slug to use. Will generate the proper
	 * rewrite rules if $rewrite is enabled. Default false.
	 *
	 * @param bool|string $has_archive Whether this CPT has archive or not.
	 * @return PostTypeBuilder
	 */
	public function set_has_archive( $has_archive ) {
		return $this->set_config( 'has_archive', $has_archive );
	}

	/**
	 * Triggers the handling of rewrites for this post type. To prevent rewrite, set to false. Defaults to true, using
	 * $post_type as slug.
	 *
	 * Prefer using https://github.com/Upstatement/routes.
	 *
	 * @param bool|array $rewrite Rewrite rules for this CPT.
	 * @return PostTypeBuilder
	 */
	public function set_rewrite( $rewrite ) {
		return $this->set_config( 'rewrite', $rewrite );
	}

	/**
	 * Sets the query_var key for this post type. Defaults to $post_type key. If false, a post type cannot be loaded at
	 * ?{query_var}={post_slug}. If specified as a string, the query ?{query_var_string}={post_slug} will be valid.
	 *
	 * @param string|bool $query_var The query var key.
	 * @return PostTypeBuilder
	 */
	public function set_query_var( $query_var ) {
		return $this->set_config( 'query_var', $query_var );
	}

	/**
	 * Whether to allow this post type to be exported. Default true.
	 *
	 * @param bool $can_export Wether to allow export or not.
	 * @return PostTypeBuilder
	 */
	public function set_can_export( bool $can_export ) {
		return $this->set_config( 'can_export', $can_export );
	}

	/**
	 * Whether to delete posts of this type when deleting a user. If true, posts of this type belonging to the user will
	 * be moved to Trash when then user is deleted. If false, posts of this type belonging to the user will *not* be
	 * trashed or deleted. If not set (the default), posts are trashed if post_type_supports('author'). Otherwise posts
	 * are not trashed or deleted. Default null.
	 *
	 * @param bool $delete_with_user Wether to delete posts when their author is deleted or not.
	 * @return PostTypeBuilder
	 */
	public function set_delete_with_user( bool $delete_with_user ) {
		return $this->set_config( 'delete_with_user', $delete_with_user );
	}
}
