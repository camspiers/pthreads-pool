# Pthreads Pool

A basic and **experimental** implementation of a thread pool for [pthreads](https://github.com/krakjoe/pthreads/)

## Example

```php
namespace Camspiers\Pthreads;

require_once 'vendor/autoload.php';

/**
 * An example Job
 */
class Job extends Work
{
    protected function process()
    {
        // Do some work, and optionally return some data
        return range(1, 1000);
    }
}

$pool = new Pool();
$jobs = array();
$count = 1000;

while ($count > 0) {
    $jobs[] = $pool->submitWork(new Job());
    $count--;
}

$pool->shutdown();

foreach ($jobs as $job) {
    var_dump($job->getData());
}
```
