<?php
namespace Gf\Git;

use GitWrapper\GitWorkingCopy;
use GitWrapper\GitWrapper;

/**
 * A wrapper to the git wrapper class
 * wrapper inception
 * Class Git
 *
 * @package Gf
 */
class GitLocal {

    /**
     * @var GitLocal[]
     */
    public static $instance;

    /**
     * @var \GitWrapper\GitWorkingCopy
     */
    public $git;

    /**
     * @var GitWrapper
     */
    public $wrapper;

    /**
     * @param $pathToRepo
     *
     * @return GitLocal
     */
    public static function instance ($pathToRepo) {
        if (!isset(static::$instance[$pathToRepo]) or null == static::$instance[$pathToRepo]) {
            static::$instance[$pathToRepo] = new static($pathToRepo);
        }

        return self::$instance[$pathToRepo];
    }

    public function __construct ($pathToRepo) {
        $this->wrapper = new GitWrapper();
        $this->git = $this->wrapper->workingCopy($pathToRepo);
    }

    public function pull ($owner_id, $provider, $clone_uri) {
        $gitApi = GitApi::instance($owner_id, $provider);
        $clone_url = $gitApi->createAuthCloneUrl($clone_uri, $provider);
        $this->setRemoteOriginUrl($clone_url);
        $this->git->clearOutput();
        $this->git->checkout('master');
        $op = $this->git->pull();

        return $op->getOutput();
    }

    public function setRemoteOriginUrl ($url) {
        $this->git->run([
            'remote',
            'set-url',
            'origin',
            $url,
        ]);
    }

    /**
     * @param $clone_url
     *
     * @return bool|string
     */
    public function clone ($clone_url) {
        if (!$this->git->isCloned()) {
            $this->git = $this->git->cloneRepository($clone_url);
            $this->git->setCloned(true);

            return $this->git->getOutput();
        }

        return false;
    }


    /**
     * @param $hash
     *
     * @return mixed
     */
    public function verifyHash ($hash) {
        try {
            $op = $this->git->run([
                'rev-parse',
                '--verify',
                $hash,
            ]);

            return trim($op->getOutput());
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get commits between two hashs
     *
     * @param $from
     * @param $to
     *
     * @return array
     */
    public function commitsBetween ($from, $to) {

        $a = $this->git->run([
            'log',
            "$from...$to",
            '--format=%H||%aN||%aE||%aD||%s',
        ]);
        $a = $a->getOutput();

        $lines = $this->split($a);

        $commits = [];

        foreach ($lines as $line) {
            list($hash, $name, $email, $date, $title) = preg_split('/\|\|/', $line, -1, PREG_SPLIT_NO_EMPTY);
            $commits[] = [
                'sha'          => $hash,
                'message'      => $title,
                'author_email' => $email,
                'author'       => $name,
                'time'         => strtotime($date),
            ];
        }

        return $commits;
    }

    /**
     * Split string by new line or null(\0)
     *
     * @param string $input   The string to split
     * @param bool   $useNull True to split by new line, otherwise null
     *
     * @return array
     */
    public function split ($input, $useNull = false) {
        if ($useNull) {
            $pattern = '/\0/';
        } else {
            $pattern = '/\r?\n/';
        }

        return preg_split($pattern, rtrim($input), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Diff changes in files from $from to $to.
     *
     * @param $from
     * @param $to
     *
     * @return array
     */
    public function diff ($from, $to) {
        $op = $this->git->run([
            'diff',
            $from,
            $to,
            '--name-status',
        ]);
        $op = $op->getOutput();

        $files = [];

        $a = explode("\n", $op);


        foreach ($a as $line) {
            $mod = substr($line, 0, 1);
            if ($mod === 'A' or $mod === 'C') {
                $files[] = [
                    'f' => trim(substr($line, 1)),
                    'a' => 'A',
                ];
            } elseif ($mod === 'M' or $mod === 'T') {
                $files[] = [
                    'f' => trim(substr($line, 1)),
                    'a' => 'M',
                ];
            } elseif ($mod == 'D' or $mod === 'T') {
                $files[] = [
                    'f' => trim(substr($line, 1)),
                    'a' => 'D',
                ];
            } else {
                // ignore this
            }
        }

        return $files;
    }

    /**
     * returns true if the hash is child of a branch.
     *
     * @param $hash
     * @param $branch
     *
     * @return bool
     */
    public function hashExistsInBranch ($hash, $branch) {

        $op = $this->git->run([
            'branch',
            '-a',
            '--contains',
            $hash,
        ]);

        if (strpos($op->getOutput(), $branch)) {
            return false;
        } else {
            return true;
        }
    }
}
