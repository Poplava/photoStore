<?php

namespace System;

class Application
{
    /**
     * @var \Pimple\Container
     */
    private static $container;

    /**
     * @param \Pimple\Container $container
     */
    public static function setContainer(\Pimple\Container $container)
    {
        self::$container = $container;
    }

    public function __construct()
    {
        spl_autoload_register([$this, 'autoload']);
    }

    public function execute($arguments)
    {
        if (!isset($arguments[1])) {
            throw new \ErrorException('Command not exists!');
        }

        $commandClass = '\\Command\\' . ucfirst($arguments[1]);
        $args = array_slice($arguments, 2);

        $instance = new $commandClass($args);
        $instance->run();
    }

    private function autoload($className)
    {
        $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

        if(is_readable($classPath)) {
            require_once $classPath;
        }
    }
}
