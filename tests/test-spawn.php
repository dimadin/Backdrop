<?php
/**
 * Tests: Tests_Spawn class
 *
 * @package Backdrop
 * @subpackage Tests
 * @since 1.0.0
 */

use dimadin\WP\Library\Backdrop\Task;
use dimadin\WP\Library\Backdrop\Server;

/**
 * Class with tests for dimadin\WP\Library\Backdrop\Task::spawn_server() method.
 *
 * @since 1.0.0
 */
class Tests_Spawn extends WP_UnitTestCase {
	/**
	 * Holds arguments passed when making HTTP request.
	 *
	 * @var array
	 */
	protected $http_request_args = [];

	/**
	 * Holds URL passed when making HTTP request.
	 *
	 * @var string
	 */
	protected $http_request_url = '';

	/**
	 * Store URL and arguments passed when making HTTP request to class properties.
	 *
	 * @param array  $args An array of HTTP request arguments.
	 * @param string $url The request URL.
	 * @return array $args
	 */
	public function filter_http_request_args( $args, $url ) {
		$this->http_request_args = $args;
		$this->http_request_url  = $url;

		return $args;
	}

	/**
	 * Test that callback has run after scheduling task and mocking server.
	 *
	 * @since 1.0.0
	 */
	public function test_callback_run() {
		$task = new Task( [ $this, 'callback_' . __FUNCTION__ ] );
		$this->assertTrue( $task->schedule() );

		// Get 'key' property of Task instance.
		$key_reflection = ( new ReflectionClass( get_class( $task ) ) )->getProperty( 'key' );

		// Set that property as accessible to be able to read it.
		$key_reflection->setAccessible( true );

		// Get property value by passing Task instance again to mock passed key.
		$key = $key_reflection->getValue( $task );

		add_filter( 'http_request_args', array( $this, 'filter_http_request_args' ), PHP_INT_MAX, 2 );

		$this->assertTrue( $task->spawn_server() );

		remove_filter( 'http_request_args', array( $this, 'filter_http_request_args' ), PHP_INT_MAX, 2 );

		$this->assertInternalType( 'array', $this->http_request_args['body'] );
		$this->assertEquals( 'md_backdrop_run', $this->http_request_args['body']['action'] );
		$this->assertEquals( $key, $this->http_request_args['body']['key'] );
		$this->assertEquals( 'POST', $this->http_request_args['method'] );
		$this->assertFalse( $this->http_request_args['blocking'] );
		$this->assertContains( 'admin-ajax.php', $this->http_request_url );
	}
}
