# Backdrop

[![Build Status](https://www.travis-ci.org/dimadin/Backdrop.svg?branch=master)](https://www.travis-ci.org/dimadin/Backdrop)
[![Latest Stable Version](https://poser.pugx.org/dimadin/Backdrop/version)](https://packagist.org/packages/dimadin/Backdropy)

Backdrop is a simple library that does one thing: allows you to run one-off
tasks in the background.

## How to Use
Backdrop is available as Composer package that you can use in your project.

```bash
composer require dimadin/backdrop
```

Alternately, you can download and include all files from `/inc` directory in your project.
In either case, you then need to register it with `Main::init()` not after `admin_init` hook.

In this example, we create task if option doesn't exist, pass parameter to it, and then schedule it,
while also hooking initialization of Backdrop:

```php
function my_awesome_function( $id ) {
	// Download initial data to my site. Might take a long time!
	$data = wp_remote_get( 'http://example.com/' . $id );

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	update_option( 'initial_data', $data );
}

add_action( 'init', function () {
	if ( ! get_option( 'initial_data' ) ) {
		$task = new \dimadin\WP\Library\Backdrop\Task( 'my_awesome_function', get_current_user_id() );
		$task->schedule();
	}
} );

add_action( 'admin_init', [ '\dimadin\WP\Library\Backdrop\Main', 'init' ] );
```

## API
Full code reference is available at [http://api.milandinic.com/backdrop/](http://api.milandinic.com/backdrop/).

### `Task::__construct( $callback [, $...] )`
Creating a new task sets up all of the internal data for your task. Pass in your
callback followed by your arguments to the function, and Backdrop will call it
in a background process.

#### Arguments
* `$callback`: Callback method you want to use. Can be any callable type
  (including object methods and static methods) **except for anonymous
  functions**. Closures cannot be serialized, so they cannot be used for
  Backdrop callbacks. This is an internal PHP limitation.
* `$...`: Any other arguments you'd like to pass to your callback, as variable
  arguments. e.g. `new Task( 'a', 'b', 'c', 'd' )` maps to `a( 'b', 'c', 'd' )`

#### Return Value
None (constructor).

### `Task::schedule()`
Schedules your task to run. Typically runs after your page has been rendered, in
a separate process.

Backdrop de-duplicates tasks based on the arguments passed in. For example, you
can do `new Task( 'myfunc', 1 )` on every request, and only one will be run.
After this has been run, the next call will schedule again.

To avoid this, you should pass in unique identifiers as needed. Everything that
makes your task unique should be passed in and used by your function, as global
state may change.

#### Arguments
None.

#### Return Value
Either `true`, or a `WP_Error` on failure. The error object will indicate the
type of error; typically this is a `md_backdrop_scheduled` if the task is
already scheduled to run or is currently running.

### `Task::is_scheduled()`
Checks whether your task is scheduled to run.

#### Arguments
None.

#### Return Value
Boolean indicating whether your task is scheduled to run, or is already running.

### `Task::cancel()`
Cancels a previously scheduled task.

Note that if the task is already running, this will not cancel execution; it
simply removes it from the tasks scheduled to run.

#### Arguments
None.

#### Return Value
Either `true`, or a `WP_Error` on failure. The error object will indicate the
type of error; typically this is a `md_backdrop_not_scheduled` if the task
hasn't been scheduled.

### `Main::init()`
Register Backdrop.

#### Arguments
None.

#### Return Value
None.

## Compatibility
Backdrop is compatible with PHP 5.4 and upwards.

## License
Backdrop is licensed under the GPL version 2.

Copyright 2014 Human Made Limited, 2015-2018 Milan Dinić
