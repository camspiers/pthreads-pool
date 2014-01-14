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
     * @var
     */
    protected $data;

    /**
     * @return mixed
     */
    abstract protected function process();

    /**
     * Runs the work and submits the data
     */
    final public function run()
    {
        $this->data = $this->process();
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}