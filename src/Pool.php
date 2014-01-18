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
     * @var array
     */
    protected $data;
    /**
     * @var callable
     */
    protected $workerCreator;
    /**
     * @var bool
     */
    protected $lazyStart;
    /**
     * @var callable
     */
    protected $nextWorkerAlgorithm;

    /**
     * @param int $workerCount
     * @param callable $workerCreator
     * @param bool $lazyStart
     */
    public function __construct($workerCount = 8, callable $workerCreator = null, $lazyStart = false)
    {
        $this->workerCount = $workerCount;
        $this->workerCreator = $workerCreator;
        $this->lazyStart = $lazyStart;
    }

    /**
     * Creates a worker
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
            if (!$this->lazyStart) {
                $this->workers[$id]->start();
            }
            $this->workers[$id]->stack($work);
            return $work;
        } else {
            $this->getNextWorker()->stack($work);
            return $work;
        }
    }

    /**
     * An algorithm for selecting workers
     * 
     * This default implementation will select the worker with the least number of stacked jobs
     * @return \Worker
     */
    protected function getNextWorker()
    {
        if ($this->nextWorkerAlgorithm !== null) {
            return call_user_func($this->nextWorkerAlgorithm, $this);
        } else {
            return $this->workers[mt_rand(0, $this->workerCount - 1)];
        }
    }

    /**
     * Start workers
     * @return array
     */
    public function start()
    {
        if ($this->lazyStart) {
            foreach ($this->workers as $worker) {
                $worker->start();
            }
        }
    }

    /**
     * Shutdown
     * @return array
     */
    public function shutdown()
    {
        foreach ($this->workers as $worker) {
            $worker->shutdown();
        }
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

    /**
     * @return \Camspiers\Pthreads\Worker[]
     */
    public function getWorkers()
    {
        return $this->workers;
    }

    /**
     * @param boolean $lazyStart
     */
    public function setLazyStart($lazyStart)
    {
        $this->lazyStart = $lazyStart;
    }

    /**
     * @return boolean
     */
    public function getLazyStart()
    {
        return $this->lazyStart;
    }

    /**
     * @param mixed $nextWorkerAlgorithm
     */
    public function setNextWorkerAlgorithm($nextWorkerAlgorithm)
    {
        $this->nextWorkerAlgorithm = $nextWorkerAlgorithm;
    }

    /**
     * @return mixed
     */
    public function getNextWorkerAlgorithm()
    {
        return $this->nextWorkerAlgorithm;
    }
}