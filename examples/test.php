<?php

namespace Camspiers\Pthreads;

require_once __DIR__ . '/../vendor/autoload.php';

function random_name($length)
{
    $random = '';
    for ($i = 0; $i < $length; $i++) {
        $random .= chr(mt_rand(48, 126));
    }
    return $random;
}

class Job extends Work
{
    protected function process()
    {
        json_decode(file_get_contents(__DIR__. '/../composer.json'), true);
    }
}

$pool = new Pool();

$count = 1000000;
$jobs = array();

while ($count > 0) {
    $jobs[] = $pool->submitWork(new Job());
//    json_decode(file_get_contents(__DIR__. '/../composer.json'), true);
    $count--;
}

$pool->shutdown();

foreach ($jobs as $job) {
//    var_dump($job->getData());
}


