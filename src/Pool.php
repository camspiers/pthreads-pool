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
            $this->getLeastStackedWorker()->stack($work);
            return $work;
        }
    }

    /**
     * @return Worker
     */
    protected function getLeastStackedWorker()
    {
        $index = 0;
        $min = $this->workers[0]->getStacked();

        for ($i = 1; $i < $this->workerCount; $i++) {
            $candidateMin = $this->workers[$i]->getStacked();
            if ($candidateMin < $min) {
                if ($candidateMin === 0) {
                    return $this->workers[$i];
                }
                $min = $candidateMin;
                $index = $i;
            }
        }
        
        return $this->workers[$index];
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
}