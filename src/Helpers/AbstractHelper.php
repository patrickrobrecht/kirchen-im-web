<?php
namespace KirchenImWeb\Helpers;

/**
 * The class AbstractHelper is an abstract superclass for all singleton helper classes.
 *
 * @package KirchenImWeb\Helpers
 */
class AbstractHelper {

    protected function __construct() {
    }

    // No cloning.
    private function __clone() {
    }

    // No wakeups.
    private function __wakeup() {
    }

    public static function getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new static;
        }
        return $instance;
    }
}
