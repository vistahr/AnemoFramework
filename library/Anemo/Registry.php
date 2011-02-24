<?php
namespace Anemo;

/**
 * Registry class to pass global variables between classes.
 */
abstract class Registry
{
	
    /**
     * Object registry provides storage for shared objects
     *
     * @var array 
     */
    private static $registry = array();
    private static $null = null;
    
    /**
     * Adds a new variable to the Registry.
     *
     * @param string $key Name of the variable
     * @param mixed $value Value of the variable
     * @throws Exception
     * @return bool 
     */
    public static function set($key, &$value) {
        if ( ! self::has($key) ) {
            self::$registry[$key] = $value;
            return true;
        } else {
            throw new Registry\Exception('Unable to set variable `' . $key . '`. It was already set.');
        }
    }

    /**
     * Tests if given $key exists in registry
     *
     * @param string $key
     * @return bool
     */
    public static function has($key)
    {
        if ( isset( self::$registry[$key] ) ) {
            return true;
        }

        return false;
    }
 
    /**
     * Returns the value of the specified $key in the Registry.
     *
     * @param string $key Name of the variable
     * @return mixed Value of the specified $key
     */
    public static function &get($key)
    {
        if ( self::has($key) ) {
            return self::$registry[$key];
        }
        return self::$null;
    }
 
    /**
     * Returns the whole Registry as an array.
     *
     * @return array Whole Registry
     */
    public static function getAll()
    {
        return self::$registry;
    }
 
    /**
     * Removes a variable from the Registry.
     *
     * @param string $key Name of the variable
     * @return bool
     */
    public static function remove($key)
    {
        if ( self::has($key) ) {
            unset(self::$registry[$key]);
            return true;
        }
        return false;
    }
 
    /**
     * Removes all variables from the Registry.
     *
     * @return void 
     */
    public static function removeAll()
    {
        self::$registry = array();
        return;
    }
}