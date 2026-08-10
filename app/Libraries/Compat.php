<?php

/**
 * Static registry bridging the CI3 "current controller" concept
 * (get_instance()) to the active CI4 controller.
 */
#[AllowDynamicProperties]
class Compat
{
    /** @var object|null */
    public static $instance = null;

    public static function set_instance($instance): void
    {
        self::$instance = $instance;
    }

    /**
     * Return the current controller instance, creating a minimal
     * standalone compat object when invoked outside a controller
     * request (e.g. CLI scripts).
     */
    public static function instance()
    {
        if (self::$instance === null) {
            try {
                self::$instance = new CI_Controller();
            } catch (\Throwable $e) {
                self::$instance = null;
            }
        }

        return self::$instance;
    }
}
