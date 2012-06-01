<?php
namespace Arkas\Command;

use \Symfony\Component\Console;

abstract class Command extends Console\Command\Command
{
    protected $container = null;

    public function setContainer(\Arkas\Application $container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function getService($name)
    {
        return isset($this->container[$name]) ? $this->container[$name] : null;
    }
}
