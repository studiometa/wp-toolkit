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
class TaxonomyBuilder extends Builder {
	/**
	 * The post type configuration.
	 *
	 * @var array
	 */
	public $default_config = array(
		'query_var'         => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_nav_menus' => true,
		'show_tagcloud'     => true,
	);

	/**
	 * The register method name.
	 *
	 * @var string
	 */
	public $register_method = 'register_taxonomy';

	/**
	 * The register method args.
	 *
	 * @var array
	 */
	public $register_method_args = array( 'type', 'post_types', 'config' );

	/**
	 * The post types which associated to this taxonomy.
	 *
	 * @var string|array
	 */
	protected $post_types = array();

	/**
	 * Set the taxonomy post types.
	 *
	 * @param string|array $post_types Object type or array of object types with which the taxonomy should be associated.
	 * @return $this;
	 */
	public function set_post_types( $post_types ):self {
		$this->post_types = $post_types;
		return $this;
	}

	/**
	 * Get the associated post types.
	 *
	 * @return string|array The name or a list of names of post types.
	 */
	public function get_post_types() {
		return $this->post_types;
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
			'add_new_item'          => sprintf( __( 'Add New %s', 'studiometa' ), $singular ),
			'all_items'             => sprintf( __( 'All %s', 'studiometa' ), $plural ),
			'choose_from_most_used' => __( 'Most used', 'studiometa' ),
			'edit_item'             => sprintf( __( 'Edit %s', 'studiometa' ), $singular ),
			'items_list'            => sprintf( __( '%s list', 'studiometa' ), $plural ),
			'items_list_navigation' => sprintf( __( '%s list navigation', 'studiometa' ), $plural ),
			'menu_name'             => $plural,
			'new_item_name'         => sprintf( __( 'New %s', 'studiometa' ), $singular ),
			'no_terms'              => sprintf( __( 'No %s', 'studiometa' ), $singular ),
			'parent_item'           => sprintf( __( '%s parent', 'studiometa' ), $singular ),
			'parent_item_colon'     => isset( $this->get_config()['hierarchical'] ) && $this->get_config()['hierarchical'] ? sprintf( __( '%s parent:', 'studiometa' ), $singular ) : null,
			'popular_items'         => sprintf( __( 'Popular %s', 'studiometa' ), $singular ),
			'search_items'          => sprintf( __( 'Search %s', 'studiometa' ), $plural ),
			'update_item'           => sprintf( __( 'Update the %s', 'studiometa' ), $singular ),
			'view_item'             => sprintf( __( 'View %s', 'studiometa' ), $singular ),
		);
		// phpcs:enable

		return $labels;
	}

	/**
	 * A short descriptive summary of what the taxonomy is for.
	 *
	 * @param string $description The description.
	 * @return $this
	 */
	public function set_description( string $description ):self {
		return $this->set_config( 'description', $description );
	}

	/**
	 * Whether a taxonomy is intended for use publicly either via the admin interface or by front-end users. The default
	 * settings of $publicly_queryable, $show_ui, and $show_in_nav_menus are inherited from $public.
	 *
	 * @param bool $public Public or not.
	 * @return $this
	 */
	public function set_public( bool $public ):self {
		return $this->set_config( 'public', $public );
	}

	/**
	 * Whether the taxonomy is publicly queryable. If not set, the default is inherited from $public.
	 *
	 * @param bool $publicly_queryable Is publicly queryable or not.
	 * @return $this
	 */
	public function set_publicly_queryable( bool $publicly_queryable ):self {
		return $this->set_config( 'publicly_queryable', $publicly_queryable );
	}

	/**
	 * Whether the taxonomy is hierarchical. Default false.
	 *
	 * @param bool $hierarchical Hierarchical or not.
	 * @return $this
	 */
	public function set_hierarchical( bool $hierarchical ):self {
		return $this->set_config( 'hierarchical', $hierarchical );
	}

	/**
	 * Whether to generate and allow a UI for managing terms in this taxonomy in the admin. If not set, the default is
	 * inherited from $public. Default to true.
	 *
	 * @param bool $show_ui Show the UI or not.
	 * @return $this
	 */
	public function set_show_ui( bool $show_ui ):self {
		return $this->set_config( 'show_ui', $show_ui );
	}

	/**
	 * Whether to show the taxonomy in the admin menu or not. If true, the taxonomy is shown as a submenu of the object
	 * type menu. If false, no menu is shown. $show_ui must be true. If not set, default is inherited from $show_ui.
	 * Default to true.
	 *
	 * @param bool $show_in_menu Show in menu or not.
	 * @return $this
	 */
	public function set_show_in_menu( bool $show_in_menu ):self {
		return $this->set_config( 'show_in_menu', $show_in_menu );
	}

	/**
	 * Makes this taxonomy available for selection in navigation menus. If not set, the default is inherited from $public.
	 * Default to true.
	 *
	 * @param bool $show_in_nav_menus Show in nav menus or not.
	 * @return $this
	 */
	public function set_show_in_nav_menus( bool $show_in_nav_menus ):self {
		return $this->set_config( 'show_in_nav_menus', $show_in_nav_menus );
	}

	/**
	 * Whether to include the taxonomy in the REST API. Set this to true for the taxonomy to be available in the
	 * block editor.
	 *
	 * @param bool $show_in_rest Show in taxonomy or not.
	 * @return $this
	 */
	public function set_show_in_rest( bool $show_in_rest ):self {
		return $this->set_config( 'show_in_rest', $show_in_rest );
	}

	/**
	 * To change the base url of REST API route. Default is $taxonomy.
	 *
	 * @param string $rest_base The REST API base route value.
	 * @return $this
	 */
	public function set_rest_base( string $rest_base ):self {
		return $this->set_config( 'rest_base', $rest_base );
	}

	/**
	 * REST API Controller class name. Default is 'WP_REST_Terms_Controller'.
	 *
	 * @param string $rest_controller_class The REST API Controller class name.
	 * @return $this
	 */
	public function set_rest_controller_class( string $rest_controller_class ):self {
		return $this->set_config( 'rest_controller_class', $rest_controller_class );
	}

	/**
	 * Whether to list the taxonomy in the Tag Cloud Widget controls. If not set, the default is inherited from $show_ui.
	 * Default to true.
	 *
	 * @param bool $show_tagcloud Show in tag cloud or not.
	 * @return $this
	 */
	public function set_show_tagcloud( bool $show_tagcloud ):self {
		return $this->set_config( 'show_tagcloud', $show_tagcloud );
	}

	/**
	 * Whether to show the taxonomy in the quick/bulk edit panel. It not set, the default is inherited from $show_ui.
	 * Default to true.
	 *
	 * @param bool $show_in_quick_edit Show in quick edit or not.
	 * @return $this
	 */
	public function set_show_in_quick_edit( bool $show_in_quick_edit ):self {
		return $this->set_config( 'show_in_quick_edit', $show_in_quick_edit );
	}

	/**
	 * Whether to display a column for the taxonomy on its post type listing screens. Default false.
	 *
	 * @param bool $show_admin_column Show or column or not.
	 * @return $this
	 */
	public function set_show_admin_column( bool $show_admin_column ):self {
		return $this->set_config( 'show_admin_column', $show_admin_column );
	}

	/**
	 * Provide a callback function for the meta box display. If not set, post_categories_meta_box() is used for
	 * hierarchical taxonomies, and post_tags_meta_box() is used for non-hierarchical. If false, no meta box is shown.
	 *
	 * @param bool|callable $meta_box_cb The callback function.
	 * @return $this
	 */
	public function set_meta_box_cb( $meta_box_cb ):self {
		return $this->set_config( 'meta_box_cb', $meta_box_cb );
	}

	/**
	 * Callback function for sanitizing taxonomy data saved from a meta box. If no callback is defined, an appropriate one
	 * is determined based on the value of $meta_box_cb.
	 *
	 * @param callable $meta_box_sanitize_cb The callback function.
	 * @return $this
	 */
	public function set_meta_box_sanitize_cb( $meta_box_sanitize_cb ):self {
		return $this->set_config( 'meta_box_sanitize_cb', $meta_box_sanitize_cb );
	}

	/**
	 * Array of capabilities for this taxonomy.
	 *
	 * @param array $capabilities Array of capabilities for this taxonomy.
	 * @return $this
	 */
	public function set_capabilities( array $capabilities ):self {
		return $this->set_config( 'capabilities', $capabilities );
	}

	/**
	 * Triggers the handling of rewrites for this taxonomy. Default true, using $taxonomy as slug. To prevent rewrite, set
	 * to false. To specify rewrite rules, an array can be passed with any of these keys:
	 *
	 * - `slug` (string) Customize the permastruct slug. Default $taxonomy key.
	 * - `with_front` (bool) Should the permastruct be prepended with WP_Rewrite::$front. Default true.
	 * - `hierarchical` (bool) Either hierarchical rewrite tag or not. Default false.
	 * - `ep_mask` (int) Assign an endpoint mask. Default EP_NONE.
	 *
	 * @param bool|array $rewrite The rewrite configuration.
	 * @return $this
	 */
	public function set_rewrite( $rewrite ):self {
		return $this->set_config( 'rewrite', $rewrite );
	}

	/**
	 * Sets the query var key for this taxonomy. Default $taxonomy key. If false, a taxonomy cannot be loaded at
	 * ?{query_var}={term_slug}. If a string, the query ?{query_var}={term_slug} will be valid.
	 *
	 * @param string|bool $query_var The query var value.
	 * @return $this
	 */
	public function set_query_var( $query_var ):self {
		return $this->set_config( 'query_var', $query_var );
	}

	/**
	 * Works much like a hook, in that it will be called when the count is updated. Default _update_post_term_count() for
	 * taxonomies attached to post types, which confirms that the objects are published before counting them. Default
	 * _update_generic_term_count() for taxonomies attached to other object types, such as users.
	 *
	 * @param callable $update_count_callback The callback function.
	 * @return $this
	 */
	public function set_update_count_callback( $update_count_callback ):self {
		return $this->set_config( 'update_count_callback', $update_count_callback );
	}

	/**
	 * Default term to be used for the taxonomy. If an array, it can have the following keys:
	 *
	 * - `name` (string) Name of default term.
	 * - `slug` (string) Slug for default term.
	 * - `description` (string) Description for default term.
	 *
	 * @param string|array $default_term The default term value.
	 * @return $this
	 */
	public function set_default_term( $default_term ):self {
		return $this->set_config( 'default_term', $default_term );
	}
}
