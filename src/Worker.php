<?php

namespace Camspiers\Pthreads;

use Worker as PWorker;

/**
 * Class Worker
 * @package Camspiers\Pthreads
 */
class Worker extends PWorker
{
    /**
     * @var
     */
    protected $data;

    /**
     * Set up the data array
     */
    public function run()
    {
        $this->data = [];
    }

    /**
     * Add data to the worker
     * @param $workName
     * @param $workData
     */
    public function addData($workName, $workData)
    {
        $this->data = array_merge($this->data, [$workName => $workData]);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}