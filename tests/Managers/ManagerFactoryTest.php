<?php

namespace Tests\Managers;

use Studiometa\WPToolkit\Managers\ManagerInterface;
use Studiometa\WPToolkit\Managers\ManagerFactory;

it(
	'should trigger the manager instances `run` method.',
	function() {
		/**
		 * Dummy manager class.
		 */
		class Manager implements ManagerInterface {
			public function run() {
				test()->has_run = true;
			}
		}

		ManagerFactory::init( array( new Manager() ) );
		expect( $this->has_run )->toBe( true );
	}
);
