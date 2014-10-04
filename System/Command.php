<?php
namespace System;

abstract class Command
{
    protected $args;

    /**
     * @var \Pimple\Container
     */
    protected static $container;

    /**
     * @param \Pimple\Container $container
     */
    public static function setContainer(\Pimple\Container $container)
    {
        self::$container = $container;
    }

    public function __construct($args)
    {
        $this->args = $args;
    }

    abstract public function run();
}
