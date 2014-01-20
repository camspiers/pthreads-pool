<?php

namespace Camspiers\Pthreads;

require_once __DIR__ . '/../vendor/autoload.php';

class SleepJob extends Work
{
    public $var;

    function __construct($var)
    {
        $this->var = $var;
    }
    
    protected function process()
    {
        // an example of executing some kind of command
        exec('sleep ' . $this->var);
    }
}

class Job1 extends SleepJob {
    public function getJob()
    {
        return new Job2(1);
    }
}

class Job2 extends SleepJob {
    public function getJob()
    {
        return new Job3(1);
    }
}

class Job3 extends SleepJob {
    public function getJob()
    {
        return false;
    }
}

$pool = new Pool();

for ($i = 0; $i < 40; $i++) {
    $pool->submitWork(new Job1(1));
}

// uses jobs as they finish. this method should be a lot faster as
// the secondary job can run as soon as the relevant primary work is complete
foreach ($pool->getFinishedJobs() as $job) {
    if ($newJob = $job->getJob()) {
        $pool->submitWork($newJob);
    } else {
        echo 'Finished', PHP_EOL;
    }
}

$pool->shutdown();