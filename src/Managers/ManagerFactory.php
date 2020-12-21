<?php
/**
 * Manager factory.
 *
 * @package    studiometa/wp-toolkit
 * @author     Studio Meta <agence@studiometa.fr>
 * @copyright  2020 Studio Meta
 * @license    https://opensource.org/licenses/MIT
 * @since      1.0.0
 * @version    1.0.0
 */

namespace Studiometa\WPToolkit\Managers;

/**
 * Manager factory class.
 */
class ManagerFactory {
	/**
	 * Run the given managers.
	 *
	 * @param ManagerInterface[] $managers A list of manager instances.
	 * @return void
	 */
	public static function init( array $managers ):void {
		foreach ( $managers as $manager ) {
			$manager->run();
		}
	}
}
