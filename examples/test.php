<?php

namespace Camspiers\Pthreads;

require_once 'vendor/autoload.php';

/**
 * An example Job
 */
class Job extends Work
{
    protected $url;

    function __construct($url)
    {
        $this->url = $url;
    }
    
    protected function process()
    {
        return file_get_contents($this->url);
    }
}

$pool = new Pool();
$jobs = array();
$count = 5000;

while ($count > 0) {
    $jobs[] = $pool->submitWork(new Job($argv[1]));
    $count--;
}

$pool->shutdown();

foreach ($jobs as $job) {
    echo strlen($job->getData()), PHP_EOL;
}