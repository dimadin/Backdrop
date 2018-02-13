<?php
/**
 * Tests: Tests_Main class
 *
 * @package Backdrop
 * @subpackage Tests
 * @since 1.0.0
 */

use dimadin\WP\Library\Backdrop\Main;

/**
 * Class with tests for \dimadin\WP\Library\Backdrop\Main class.
 *
 * @since 1.0.0
 */
class Tests_Main extends WP_UnitTestCase {
	/**
	 * Test that hooking spawner listener works.
	 *
	 * @since 1.0.0
	 */
	public function test_scheduling_task_hooks_spawning() {
		Main::init();
		$this->assertTrue( is_int( has_action( 'wp_ajax_nopriv_md_backdrop_run', 'dimadin\WP\Library\Backdrop\Server::spawn' ) ) );
	}
}
