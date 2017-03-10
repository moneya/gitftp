<?php

class Job extends Thread {
    public $val;

    public function __construct ($val) {
        $this->val = $val;
        $this->done = false;
    }

    public function run () {
        if (!$this->worker->ready)
            return false;

        sleep(1);
//        $list = ftp_nlist($this->worker->res, '/');
        $this->done = true;
        var_dump('Its me' . $this->val);
    }
}

class MyWorker extends Worker {
    public $say = null;
    public $ready = false;

    public function __construct () {
        $res = ftp_connect('localhost', 21, 90);
        if ($res) echo "connected \n";
        else echo "not connected \n";

        $l = ftp_login($res, 'r', 'r');
        if ($l) echo "login! \n";
        else echo "not login! \n";

        $this->ready = ($res and $l);
        $this->res = $res;
    }

    public function run () {
        echo "This is run just once for each worker. " . $this->getSomething() . " \n ";
    }

    public function getSomething () {
        return $this->say;
    }
}

// At most 3 threads will work at once
$p = new Pool(2, \MyWorker::class);

$tasks = [
    new Job('0'),
    new Job('1'),
    new Job('2'),
    new Job('3'),
    new Job('4'),
    new Job('5'),
    new Job('6'),
    new Job('7'),
    new Job('8'),
    new Job('9'),
    new Job('10'),
];

// Add tasks to pool queue
foreach ($tasks as $task) {
    $p->submit($task);
}

// shutdown will wait for current queue to be completed
$p->shutdown();
// garbage collection check / read results
$p->collect(function ($checkingTask) {
    var_dump($checkingTask);
    echo $checkingTask->val . "\n";
//    return $checkingTask->isGarbage();
});
