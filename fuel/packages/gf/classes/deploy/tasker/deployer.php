<?php

namespace Gf\Deploy\Tasker;

use Fuel\Core\File;
use Gf\Deploy\Connection;
use Gf\Deploy\Deploy;
use Gf\Exception\AppException;
use Gf\Git\GitLocal;
use Gf\Record;
use League\Flysystem\Filesystem;

class Deployer {
    const method_pthreads = 'pt';
    const method_regular = 'r';
    private static $instance;

    /**
     * The method that will be used for uploading files.
     *
     * @var
     */
    public $method;

    /**
     * Holds the server data
     *
     * @var array
     */
    public $connectionParams;

    /**
     * @var
     */
    private $files;

    /**
     * The number of processed files.
     *
     * @var int
     */
    public $progress = 0;

    /**
     * number of files, which is the total progress.
     *
     * @var int
     */
    public $total_progress = 0;

    /**
     * Messages that come up during the uploading
     *
     * @var string
     */
    public $messages = '';

    /**
     * @var Filesystem
     */
    private $connection;
    private $server;
    private $record;

    /**
     * @var GitLocal
     */
    private $gitLocal;

    /**
     * @param null             $method
     * @param \Gf\Git\GitLocal $gitLocal
     * @param array            $connectionParams
     *
     * @return \Gf\Deploy\Tasker\Deployer
     */
    public static function instance ($method = null, GitLocal $gitLocal, Array $connectionParams) {
        if (!isset(static::$instance) or null == static::$instance) {
            static::$instance = new static($method, $gitLocal, $connectionParams);
        }

        return self::$instance;
    }

    /**
     * @param mixed $record
     *
     * @return $this
     */
    public function setRecord ($record) {
        $this->record = $record;

        return $this;
    }

    /**
     * @param mixed $server
     *
     * @return $this
     */
    public function setServer ($server) {
        $this->server = $server;

        return $this;
    }


    /**
     * Deployer constructor.
     *
     * @param null             $method
     * @param \Gf\Git\GitLocal $gitLocal
     * @param array            $connectionParams
     */
    public function __construct ($method = null, GitLocal $gitLocal, Array $connectionParams) {
        $this->gitLocal = $gitLocal;
        $this->method = $method;
        $this->connectionParams = $connectionParams;
        $this->connect();

        if (is_null($this->method))
            $this->method = self::method_pthreads;

        if ($this->method == self::method_pthreads) {
            $extensions = get_loaded_extensions();
            $extensions = array_flip($extensions);
            if (!isset($extensions['pthreads'])) {
                // sad, retreat to regular
                $this->method = self::method_regular;
            }
        }
    }

    public function getMethod () {
        return $this->method;
    }

    /**
     * Clear the current queue of files.
     *
     * @return $this
     */
    public function clearFiles () {
        $this->files = [];
        $this->total_progress = 0;

        return $this;
    }


    /**
     * The files should be in format
     * ['path' => 'full/path/to/file', 'action' => upload|delete]
     *
     * @param $files
     *
     * @return $this
     */
    public function addFiles (Array $files) {
        $this->files = $files;
        $this->total_progress = count($this->files);

        return $this;
    }

    public function log ($a) {
        $this->messages .= "$a\n";
    }

    /**
     * @return string
     */
    public function getMessages () {
        $a = $this->messages;
        $this->messages = '';

        return $a;
    }

    public function clearMessages () {
        $this->messages = '';
    }

    /**
     * @return int
     */
    public function getProgress () {
        return $this->progress;
    }


    /**
     * Give in the database connection.
     *
     * @param $connection
     *
     * @return $this
     */
    public function setConnection ($connection) {
        $this->connection = $connection;

        return $this;
    }

    public function connect () {
        if (!$this->connectionParams or !count($this->connectionParams))
            throw new AppException('Connection params not found');

        $connection = Connection::instance($this->connectionParams);
        $this->setConnection($connection->connection());
    }

    /**
     * inserts the progress in the database
     *
     * @param int $number
     */
    private function incrementProgress ($number = 1) {
        $this->progress += $number;

        if ($this->progress % 2 == 0 || $this->progress == $this->total_progress)
            Record::update([
                'id' => $this->record['id'],
            ], [
                'processed_files' => $this->progress,
            ]);
    }

    /**
     * Start the deployment process
     */
    public function start () {
        // hey boo
        if ($this->method == self::method_pthreads)
            return $this->pThreads();
        elseif ($this->method == self::method_regular)
            return $this->regular();
        else
            return false;
    }

    private function regular () {
        $r = $this->record['target_revision'];
        $dir = $this->gitLocal->git->getDirectory();

        $this->log('Upload method: regular');
        foreach ($this->files as $file) {
            if ($file['a'] == Deploy::file_added or $file['a'] == Deploy::file_modified) {
                $s = $dir . $file['f'];
                $contents = File::read($s, true);
                try {
                    $this->connection->put($file['f'], $contents);
                } catch (\Exception $e) {
                    $this->log("WARN: {$e->getMessage()} {$file['f']}");
                }
                $this->incrementProgress(1);
            } elseif ($file['a'] == Deploy::file_deleted) {
                try {
                    $this->connection->delete($file['f']);
                } catch (\Exception $e) {
                    $this->log("WARN: {$e->getMessage()} {$file['f']}");
                }
                $this->incrementProgress(1);
            } else {
                throw new AppException('Invalid file action type');
            }
        }

        return true;
    }

    private function pThreads () {
//        $workPool = new \Pool(2, ConnectionWorker::class, [
//            $this->currentServer,
//            $this->currentRecord,
//            $this->git,
//            $connection->connection(),
//        ]);
//
//        foreach ($allFiles as $fileAction) {
//            $workPool->submit(new FileTask($fileAction));
//        }
//
//        $workPool->shutdown();
//
//        $workPool->collect(function ($checkingTask) {
//            var_dump($checkingTask);
//        });

        return true;
    }

}
