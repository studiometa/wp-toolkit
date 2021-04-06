<?php
/**
 * Delete WordPress transients on content save, based on user-input configuration.
 *
 * @package    studiometa/wp-toolkit
 * @author     Studio Meta <agence@studiometa.fr>
 * @copyright  2020 Studio Meta
 * @license    https://opensource.org/licenses/MIT
 * @since      1.0.0
 * @version    1.0.0
 */

namespace Studiometa\WPToolkit;

/**
 * TransientCleaner class.
 */
class TransientCleaner {
	const PREFIX                   = 'wp_transient_cleaner_';
	const OPTION_STORED_TRANSIENTS = self::PREFIX . 'stored_transients';

	/**
	 * Class instance
	 *
	 * @var object|null
	 */
	private static $instance = null;

	/**
	 * Transients configuration.
	 *
	 * @var array
	 */
	private $config = array();

	/**
	 * Stored transients.
	 *
	 * @var array|bool
	 */
	private $stored_transients;

	/**
	 * Constructor.
	 *
	 * @param array $config Configuration.
	 */
	public function __construct( $config = array() ) {
		$this->set_stored_transients( get_option( self::OPTION_STORED_TRANSIENTS ) );
		$this->set_config( $config );
		$this->define_public_hooks();
	}

	/**
	 * Get class instance.
	 *
	 * @param array $config Configuration.
	 *
	 * @return object Class instance
	 */
	public static function get_instance( $config = array() ) {
		if ( is_null( self::$instance ) ) {
			self::$instance = new TransientCleaner( $config );
		}

		return self::$instance;
	}

	/**
	 * Initialize hooks and filters.
	 *
	 * @return void
	 */
	public function define_public_hooks() {
		add_action( 'save_post', array( $this, 'post_transient_cleaner' ), 10, 2 );
		add_action( 'edit_term', array( $this, 'term_transient_cleaner' ), 10, 3 );
		add_action( 'updated_option', array( $this, 'option_transient_cleaner' ), 10, 2 );
		add_action( 'setted_transient', array( $this, 'store_transient_key' ) );
		add_filter( 'pre_update_option_' . self::OPTION_STORED_TRANSIENTS, array( $this, 'merge_stored_transients_option_values' ), 10, 3 );
		add_filter( 'update_option_' . self::OPTION_STORED_TRANSIENTS, array( $this, 'set_stored_transients' ), 20, 1 );
	}

	/**
	 * Get config.
	 *
	 * @return array
	 */
	public function get_config() {
		return $this->config;
	}

	/**
	 * Set config.
	 *
	 * @param array $config Configuration.
	 *
	 * @return object
	 */
	public function set_config( array $config ) {
		$this->config = $config;

		return $this;
	}

	/**
	 * Get stored_transients.
	 *
	 * @return array|bool
	 */
	public function get_stored_transients() {
		return $this->stored_transients;
	}

	/**
	 * Set stored_transients.
	 *
	 * @param array|bool $value New value.
	 *
	 * @return object
	 */
	public function set_stored_transients( $value ) {
		$this->stored_transients = $value;

		return $this;
	}

	/**
	 * Merge new stored transient key with others if exists.
	 *
	 * @param array|bool $value     New value.
	 * @param array|bool $old_value Old value.
	 * @param string     $option    Option.
	 *
	 * @return array|bool           New value
	 */
	public function merge_stored_transients_option_values( $value, $old_value, $option = null ) {
		// Return `$value` if no previous value.
		if ( false === $old_value ) {
			return $value;
		}

		// Do nothing if transient key already exists in stored transients.
		if ( is_array( $value ) && is_array( $old_value ) ) {
			if ( isset( $value[0] ) && true === in_array( $value[0], $old_value, true ) ) {
				return $old_value;
			}

			// Merge old and new values.
			return array_merge( $old_value, $value );
		}

		return $old_value;
	}

	/**
	 * Store transient key in option on save.
	 *
	 * @param string $transient_key Transient key.
	 *
	 * @see self::merge_stored_transients_option_values
	 *
	 * @return bool
	 */
	public function store_transient_key( string $transient_key ) {
		if (
			false === strpos( $transient_key, self::PREFIX )
			|| false !== strpos( $transient_key, '_lock' )
		) {
			return false;
		}

		return update_option(
			self::OPTION_STORED_TRANSIENTS,
			array( $transient_key )
		);
	}

	/**
	 * Delete transient based on current content and transient key.
	 *
	 * @param string   $type       Content type.
	 * @param callable $validator  Function who control if the transient must be validate or no (must return boolean).
	 *
	 * @return bool
	 */
	protected function object_transient_cleaner( string $type, callable $validator ) {
		if ( ! is_array( $this->stored_transients ) || empty( $this->config[ $type ] ) ) {
			return false;
		}

		foreach ( $this->config[ $type ] as $type_key => $type_values ) {
			if ( 'all' !== $type_key && false === call_user_func( $validator, $type_key ) ) {
				continue;
			}

			foreach ( $type_values as $transient_key ) {
				foreach ( $this->stored_transients as $stored_transient ) {
					if ( false === strpos( $stored_transient, $transient_key ) ) {
						continue;
					}

					delete_transient( $stored_transient );
				}
			}
		}

		return true;
	}

	/**
	 * Clear transient on post save.
	 *
	 * @param mixed $post_id post id.
	 * @param mixed $post post.
	 *
	 * @return bool
	 */
	public function post_transient_cleaner( $post_id, $post ) {
		return $this->object_transient_cleaner(
			'post',
			function( $key ) use ( $post ) {
				return $key === $post->post_type;
			}
		);
	}

	/**
	 * Clear transient on term save.
	 *
	 * @param int    $term_id  Term ID.
	 * @param int    $tt_id    Term taxonomy ID.
	 * @param string $taxonomy Taxonomy.
	 *
	 * @return bool
	 */
	public function term_transient_cleaner( int $term_id, int $tt_id, string $taxonomy ) {
		return $this->object_transient_cleaner(
			'term',
			function( $key ) use ( $taxonomy ) {
				return $key === $taxonomy;
			}
		);
	}

	/**
	 * Clear transient on option save.
	 *
	 * @param string $option Option key.
	 *
	 * @return bool
	 */
	public function option_transient_cleaner( string $option ) {
		return $this->object_transient_cleaner(
			'option',
			function( $key ) use ( $option ) {
				return strpos( $option, $key );
			}
		);
	}
}
