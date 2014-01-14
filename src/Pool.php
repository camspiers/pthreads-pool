<?php

namespace Camspiers\Pthreads;

/**
 * Class Pool
 * @package Camspiers\Pthreads
 */
class Pool
{
    /**
     * @var \Camspiers\Pthreads\Worker[]
     */
    protected $workers = [];
    /**
     * @var int
     */
    protected $workerCount;
    /**
     * @var int
     */
    protected $currentWorker = 0;
    /**
     * @var bool
     */
    protected $workersShutdown = false;
    /**
     * @var array
     */
    protected $data;
    /**
     * @var callable
     */
    protected $workerCreator;

    /**
     * @param int $workerCount
     * @param callable $workerCreator
     */
    public function __construct($workerCount = 8, callable $workerCreator = null)
    {
        $this->workerCount = $workerCount;
        $this->workerCreator = $workerCreator;
    }

    /**
     * @return \Camspiers\Pthreads\Worker
     */
    protected function createWorker()
    {
        if ($this->workerCreator !== null) {
            return call_user_func($this->workerCreator);
        } else {
            return new Worker();
        }
    }

    /**
     * @param \Camspiers\Pthreads\Work $work
     * @return \Camspiers\Pthreads\Work
     */
    public function submitWork(Work $work)
    {
        if (count($this->workers) < $this->workerCount) {
            $id = count($this->workers);
            $this->workers[$id] = $this->createWorker();
            $this->workers[$id]->start();
            $this->workers[$id]->stack($work);
            return $work;
        } else {
            $this->currentWorker = ($this->currentWorker + 1) % $this->workerCount;
            $this->workers[$this->currentWorker]->stack($work);
            return $work;
        }
    }

    /**
     * Gets data from all work
     * @return array
     */
    public function getData()
    {
        if ($this->data === null) {
            foreach ($this->workers as $worker) {
                $worker->shutdown();
            }
            
            $data = [];

            foreach ($this->workers as $worker) {
                $data[] = $worker->getData();
            }

            $this->data = call_user_func_array('array_merge', $data);
        }
        
        return $this->data;
    }

    /**
     * Returns the data for the specified work
     * 
     * This results in the shutdown of all threads
     * @param Work $work
     * @return mixed
     */
    public function getWorkData(Work $work)
    {
        return $this->getData()[$work->getName()];
    }

    /**
     * @param int $workerCount
     */
    public function setWorkerCount($workerCount)
    {
        $this->workerCount = $workerCount;
    }

    /**
     * @return int
     */
    public function getWorkerCount()
    {
        return $this->workerCount;
    }

    /**
     * @param callable $workerCreator
     */
    public function setWorkerCreator($workerCreator)
    {
        $this->workerCreator = $workerCreator;
    }

    /**
     * @return callable
     */
    public function getWorkerCreator()
    {
        return $this->workerCreator;
    }
}