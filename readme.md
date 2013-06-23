Athletic
========

Athletic is a benchmarking framework.  It allows developers to easily create benchmarks of their code without littering microtime() calls everywhere.

Athletic was inspired by the annotation format that PHPUnit uses.  Special benchmark tests are created which extend the `AthleticEvent` class.  Benchmark functions are then annotated appropriately and run with the Athletic command-line tool.

Why Benchmark?
--------------
Because fast code is good.  While premature optimization is certainly evil, optimization is always an important component of software development.  And sometimes you just really need to see if one solution to a problem is faster than an alternative.

Why Use Athletic?
------------------------------------------
Because it makes benchmarking easy!  Athletic is built around annotations.  Simply create a benchmarking class and annotate a few methods:

```php
    /**
     * @iterations 1000
     */
    public function fastIndexingAlgo()
    {
        $this->fast->index($this->data);
    }
```

Without Athletic, you have to litter your code with microtime() calls and build timing metrics yourself.  Or you end up building a benchmark harness framework similar to Athletic (but probably with less syntactic sugar...because who builds throw-away code to read annotations?)

Why can't I use xDebug?
-----------------------

xDebug is an excellent profiling tool, but it is not a benchmarking tool.  xdebug (and by extension, cachegrind) will show you what is fast/slow inside your method, and is indespensible for actually optimizing your code.  But it is not useful for running 1000 iterations of a particular function and determining average execution time.

Quick Installation via Composer
-------------------------------
You can easily install Athletic through [Composer](http://getcomposer.org) in two steps:

    # Install Composer
    curl -sS https://getcomposer.org/installer | php

    # Add Athletic as a dependency
    php composer.phar require athletic/athletic:~0.1

Installation via Composer
-------------------------
A more manual installation involves editing your ``composer.json`` file:

1. Add ``athletic/athletic`` as a dependency in your project's ``composer.json`` file:

        {
            "require-dev": {
                "athletic/athletic": "~0.1"
            }
        }

2. Download and install Composer:

        curl -s http://getcomposer.org/installer | php

3. Install your dependencies:

        php composer.phar install

You can find out more on how to install Composer, configure autoloading, and other best-practices for defining dependencies at [getcomposer.org](http://getcomposer.org).

Usage
-----

To begin using Athletic, you must create an "Event" file.  This is the analog of a PHPUnit Test Case.  An Event will benchmark one or more functions related to your code, compile the results and output them to the command line.

Here is a sample Event:

```php
<?php

namespace Vendor\Package\Benchmarks\Indexing;

use Vendor\Package\Indexing;
use Athletic\AthleticEvent;

class IndexingEvent extends AthleticEvent
{
    private $fast;

    private $slow;

    private $data;

    public function setUp()
    {
        $this->fast = new Indexing\FastIndexer();
        $this->slow = new Indexing\SlowIndexer();
        $this->data = array('field' => 'value');
    }


    /**
     * @iterations 1000
     */
    public function fastIndexingAlgo()
    {
        $this->fast->index($this->data);
    }


    /**
     * @iterations 1000
     */
    public function slowIndexingAlgo()
    {
        $this->slow->index($this->data);
    }

}
```

Let's look at how this works.
```php
<?php

namespace Vendor\Package\Benchmarks\Indexing;

use Vendor\Package\Indexing;
use Athletic\AthleticEvent;
```
First, we have a PHP file that is included in your project's repo, just like unit tests.  In this example, the Event is saved under the `Vendor\Package\Benchmarks\Indexing` namespace.  It uses classes from your project located at `Vendor\Package\Indexing`.  It also uses a class from the Athletic framework.

```php
class IndexingEvent extends AthleticEvent
{
```
Next, we declare an indexing class that extends \Athletic\AthleticEvent.  This is important because it tells Athletic that this class should be benchmarked.  AthleticEvent is an abstract class that provides code to inspect your  class and actually run the benchmarks.

```php
    private $fast;

    private $slow;

    private $data;

    public function setUp()
    {
        $this->fast = new Indexing\FastIndexer();
        $this->slow = new Indexing\SlowIndexer();
        $this->data = array('field' => 'value');
    }
```
Next, we have some private variables and a setUp() method.  Like PHPUnit, `setUp()` is invoked once at the beginning of each benchmark run.  This is a good place to instantiate variables that are important to the benchmark itself, populate data, build database connections, etc.  In this example, we are building two "Indexing" classes and a sample piece of data.  *(More details about setup and tear down are further down in this document)*

```php
    /**
     * @iterations 1000
     */
    public function fastIndexingAlgo()
    {
        $this->fast->index($this->data);
    }


    /**
     * @iterations 1000
     */
    public function slowIndexingAlgo()
    {
        $this->slow->index($this->data);
    }
```
Finally, we get to the meat of the benchmark.  Here we have two methods that are annotated with `@iterations` in the docblock.  The `@iterations` annotation tells Athletic how many times to repeat the method.  If a method does not have an iterations annotation, it will not be benchmarked.

Benchmark Output
----------------

So what does the output of a benchmark look like?

```
Vendor\Package\Benchmarks\Indexing\IndexingEvent
    Method Name             Iterations    Average Time      Ops/second
    ---------------------  ------------  --------------    -------------
    fastIndexingAlgo:      [1000      ] [0.0020904064178] [478.37588]
    slowIndexingAlgo:      [1000      ] [0.0048114223480] [177.59184]
```

The default formatter outputs the Event class name, each method name, the number of iterations, average time and operations per second.  More advanced formatters will be created in the near future (CSVFormatter, database export, advanced statistics, etc).

Further Information
===================

SetUps and TearDowns
--------------------
Athletic offers several methods to setup and tear down data/variables.

 - classSetUp() : invoked at the beginning of the Event before anything else has ocurred
 - setUp() : invoked once before each iteration of the method being benchmark.
 - classTearDown() : invoked at the end of the event after everything else has ocurred
 - tearDown() : invoked after each iteration of a benchmark has completed.

There are two levels of setup and tear down to prevent "state leakage" between benchmarks.  For example, an object that caches calculations will perform faster on subsequent calls to the method.  If the goal is to benchmark the initial calculation, it makes sense to place the instantiation of the object in setUp().  If the goal, however, is to benchmark the entire process (initial calculation and subsequent caching), then it makes more sense to instantiate the object in classSetUp() so that it is only built once.

Calibration
-----------

Athletic uses Reflection and variable functions to invoke the methods in your Event.  Because there is some internal overhead to variable functions, Athletic performs a "calibration" step before each iteration.  This step calls an empty calibration method and times how long it takes.  This time is then subtracted from the iterations total time, providing a more accurate total time.