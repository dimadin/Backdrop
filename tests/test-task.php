<?php
/**
 * Tests: Tests_Task class
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
class Tests_Task extends WP_UnitTestCase {
	/**
	 * Test scheduling of task.
	 *
	 * @since 1.0.0
	 */
	public function test_task_scheduling() {
		$task = new Task( [ $this, 'callback_' . __FUNCTION__ ] );
		$this->assertTrue( $task->schedule() );
	}

	/**
	 * Test checking scheduling status of already scheduled task.
	 *
	 * @since 1.0.0
	 */
	public function test_task_scheduling_check() {
		$task = new Task( [ $this, 'callback_' . __FUNCTION__ ] );
		$this->assertTrue( $task->schedule() );
		$this->assertTrue( $task->is_scheduled() );
	}

	/**
	 * Test canceling of scheduled task.
	 *
	 * @since 1.0.0
	 */
	public function test_task_canceling() {
		$task = new Task( [ $this, 'callback_' . __FUNCTION__ ] );
		$this->assertTrue( $task->schedule() );
		$this->assertTrue( $task->is_scheduled() );
		$this->assertTrue( $task->cancel() );
		$this->assertFalse( $task->is_scheduled() );
	}

	/**
	 * Test scheduling of scheduled task.
	 *
	 * @since 1.0.0
	 */
	public function test_task_scheduling_of_scheduled() {
		$task = new Task( [ $this, 'callback_' . __FUNCTION__ ] );
		$this->assertTrue( $task->schedule() );
		$this->assertTrue( $task->is_scheduled() );
		$this->assertWPError( $task->schedule() );
	}

	/**
	 * Test that scheduling task hooks callback for spawning.
	 *
	 * @since 1.0.0
	 */
	public function test_scheduling_task_hooks_spawning() {
		$task = new Task( [ $this, 'callback_' . __FUNCTION__ ] );
		$this->assertTrue( $task->schedule() );
		$this->assertTrue( $task->is_scheduled() );
		$this->assertTrue( is_int( has_action( 'shutdown', [ $task, 'spawn_server' ] ) ) );
	}

	/**
	 * Test canceling of scheduled task.
	 *
	 * @since 1.0.0
	 */
	public function test_canceling_scheduled_task_unhooks_spawning() {
		$task = new Task( [ $this, 'callback_' . __FUNCTION__ ] );
		$this->assertTrue( $task->schedule() );
		$this->assertTrue( $task->is_scheduled() );
		$this->assertTrue( is_int( has_action( 'shutdown', [ $task, 'spawn_server' ] ) ) );
		$this->assertTrue( $task->cancel() );
		$this->assertFalse( $task->is_scheduled() );
	}
}
