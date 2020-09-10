<?php

namespace Wordcamp;

trait Singleton {

	protected static $instance;

	private function __construct() {
		$this->init();
	}

	private function __clone() {

	}

	private function __wakeup() {

	}

	final public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	protected abstract function init();

}
