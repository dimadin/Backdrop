<?php
/**
 * Tests: Tests_Server class
 *
 * @package Backdrop
 * @subpackage Tests
 * @since 1.0.0
 */

use dimadin\WP\Library\Backdrop\Task;
use dimadin\WP\Library\Backdrop\Server;

/**
 * Class with tests for \dimadin\WP\Library\Backdrop\Task class.
 *
 * @since 1.0.0
 */
class Tests_Server extends WP_UnitTestCase {
	/**
	 * Basic callback that is run on scheduled task.
	 *
	 * @since 1.0.0
	 */
	public function callback_test_basic() {
		echo 'Works!';
	}

	/**
	 * Callback that accepts parameters that is run on scheduled task.
	 *
	 * @since 1.0.0
	 *
	 * @param string $first First parameter.
	 * @param string $second Second parameter.
	 */
	public function callback_test_params( $first, $second ) {
		echo 'Passing' . $first . $second; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Test that callback has run after scheduling task and mocking server.
	 *
	 * @since 1.0.0
	 */
	public function test_callback_run() {
		$task = new Task( [ $this, 'callback_test_basic' ] );
		$this->assertTrue( $task->schedule() );

		// Get 'key' property of Task instance.
		$key_reflection = ( new ReflectionClass( get_class( $task ) ) )->getProperty( 'key' );

		// Set that property as accessible to be able to read it.
		$key_reflection->setAccessible( true );

		// Get property value by passing Task instance again to mock passed key.
		$key = $key_reflection->getValue( $task );

		$_POST['key'] = $key;

		ob_start();
		( new Server() )->run();
		$output = ob_get_clean();

		// Test content of buffer.
		$this->assertEquals( 'Works!', $output );

		// Test that task has been deleted.
		$this->assertFalse( $task->is_scheduled() );
		$this->assertFalse( WP_Temporary::get( 'md_backdrop-' . $key ) );
	}

	/**
	 * Test that callback with params has outputted them during mocked run.
	 *
	 * @since 1.0.0
	 */
	public function test_callback_with_params() {
		$task = new Task( [ $this, 'callback_test_params' ], 'Params', 'Works!' );
		$this->assertTrue( $task->schedule() );

		// Get 'key' property of Task instance.
		$key_reflection = ( new ReflectionClass( get_class( $task ) ) )->getProperty( 'key' );

		// Set that property as accessible to be able to read it.
		$key_reflection->setAccessible( true );

		// Get property value by passing Task instance again to mock passed key.
		$key = $key_reflection->getValue( $task );

		$_POST['key'] = $key;

		ob_start();
		( new Server() )->run();
		$output = ob_get_clean();

		// Test content of buffer.
		$this->assertEquals( 'PassingParamsWorks!', $output );

		// Test that task has been deleted.
		$this->assertFalse( $task->is_scheduled() );
		$this->assertFalse( WP_Temporary::get( 'md_backdrop-' . $key ) );
	}
}
