<?php
/**
 * Interface for Managers.
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
 * Manager interface.
 */
interface ManagerInterface {
	/**
	 * Runs initialization tasks.
	 *
	 * @return void
	 */
	public function run();
}
