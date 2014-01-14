<?php

namespace Camspiers\Pthreads;

use Stackable;

/**
 * Class Work
 * @package Camspiers\Pthreads
 */
abstract class Work extends Stackable
{
    /**
     * A unique name that can later be used to retrieve data off pool
     * @return mixed
     */
    abstract public function getName();

    /**
     * The data that results from the
     * @return mixed
     */
    abstract protected function getData();

    /**
     * Runs the work and submits the data
     */
    final public function run()
    {
        $this->worker->addData($this->getName(), $this->getData());
    }
}