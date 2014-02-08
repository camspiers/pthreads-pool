<?php

namespace Camspiers\Pthreads;

use Composer\Autoload\ClassLoader;

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
    protected $jobs = [];
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
     * @var \Composer\Autoload\ClassLoader
     */
    protected $loader;

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
            return new Worker($this->loader);
        }
    }

    /**
     * @param \Camspiers\Pthreads\Work $work
     * @param null $key
     * @throws \RuntimeException
     * @return \Camspiers\Pthreads\Work
     */
    public function submitWork(Work $work, $key = null)
    {
        if (count($this->workers) < $this->workerCount) {
            $id = count($this->workers);
            $this->workers[$id] = $this->createWorker();
            if (!$this->lazyStart) {
                $this->workers[$id]->start();
            }
            $this->workers[$id]->stack($work);
        } else {
            $this->getNextWorker()->stack($work);
        }
        
        if ($key === null) {
            $this->jobs[] = $work;
        } elseif(empty($this->jobs[$key])) {
            $this->jobs[$key] = $work;
        } else {
            throw new \RuntimeException(sprintf("Job with key '%s' already exists", $key));
        }

        return $work;
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
     * Yields jobs as they finish
     * @return \Generator
     */
    public function getFinishedJobs()
    {
        while (count($this->jobs) !== 0) {
            foreach ($this->jobs as $index => $job) {
                if ($job->isFinished()) {
                    unset($this->jobs[$index]);
                    yield $index => $job;
                }
            }
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

    /**
     * @return \Camspiers\Pthreads\Work[]
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * @param \Composer\Autoload\ClassLoader $loader
     */
    public function setLoader(ClassLoader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }
}