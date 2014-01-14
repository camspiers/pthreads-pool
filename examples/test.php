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

class MathJob extends Work
{
    protected $name;
    
    public function __construct($name)
    {
        $this->name = $name;
    }
    public function getName()
    {   
        return $this->name;
    }
    protected function getData()
    {
        return array();
    }
}

$pool = new Pool();

$count = 10000;
$jobs = array();

while ($count > 0) {
    $jobs[] = $pool->submitWork(new MathJob(random_name(100)));
    $count--;
}

$pool->getData();


