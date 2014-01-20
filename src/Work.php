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
     * @var bool
     */
    protected $finished = false;

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
        $this->finished = true;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isFinished()
    {
        return $this->finished;
    }
}