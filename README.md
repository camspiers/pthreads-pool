# Pthreads Pool

A basic and **experimental** implementation of a thread pool for [pthreads](https://github.com/krakjoe/pthreads/)

## Example

```php
namespace Camspiers\Pthreads;

require_once 'vendor/autoload.php';

class Job extends Work
{
    protected function process()
    {
        // Do some work, and optionally return some data
        return range(1, 1000);
    }
}

$pool = new Pool();

for ($i = 0; $i < 1000; $i++) {
    $pool->submitWork(new Job());
}

// get jobs as they finish
foreach ($pool->getFinishedJobs() as $job) {
    var_dump($job->getData());
}

$pool->shutdown();
```

## Working with an autoloader

In `pthreads` you need to register a autoload in each thread (or worker). The can be achieved by setting a loader
on the pool.

```php
$loader = require 'vendor/autoload.php';

$pool = new \Camspiers\Pthreads\Pool();
$pool->setLoader($loader);

// Use the pool
```
