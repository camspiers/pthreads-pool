<?php

namespace Camspiers\Pthreads;

use Composer\Autoload\ClassLoader;
use Worker as PWorker;

/**
 * Class Worker
 * @package Camspiers\Pthreads
 */
class Worker extends PWorker
{
    /**
     * @var \Composer\Autoload\ClassLoader
     */
    protected $loader;

    /**
     * @param \Composer\Autoload\ClassLoader|void $loader
     */
    public function __construct(ClassLoader $loader = null)
    {
        $this->loader = $loader;
    }

    /**
     * If there is an autoloader register it
     * @return void
     */
    public function run()
    {
        if ($this->loader) {
            $this->loader->register();
        }
    }
}