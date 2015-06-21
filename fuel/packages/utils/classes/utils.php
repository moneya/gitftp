<?php

class utils {

    /**
     * Executes an Git command and returns the results.
     * if there is no output returns false.
     * $repo,
     * $username,
     * $password,
     *
     * @param type $arg
     */
    public static function gitGetBranches($repo, $username = NULL, $password = NULL) {

        $repo_url = parse_url($repo);

        if (!is_null($username)) {
            $repo_url['user'] = $username;
        }
        if (!is_null($password)) {
            $repo_url['pass'] = $password;
        }
        $repo = http_build_url($repo_url);

        if (trim($repo) == '') {
            return FALSE;
        }
        exec("git ls-remote --heads $repo", $op);
        if (empty($op)) return FALSE;

        foreach ($op as $k => $v) {
            $b = preg_split('/\s+/', $v);
            $b = explode('/', $b[1]);
            $op[$k] = $b[2];
        }

        return $op;
    }

    /**
     * Get avatar of an email address.
     *
     * @param type $email
     * @param type $s
     * @param type $d
     * @param type $r
     * @param type $img
     * @param type $atts
     * @return string
     */
    public static function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = FALSE, $atts = array()) {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val) $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }

        return $url;
    }

    /**
     * Test a ftp server, and if the path exists.
     *
     * @param type $a
     * @return string
     */
    public static function test_ftp($a = array()) {
        $b = array('hostname' => $a['host'], 'username' => $a['username'], 'password' => $a['pass'], 'timeout' => 30, 'port' => $a['port'], 'passive' => TRUE, 'ssl_mode' => ($a['scheme'] == 'ftps') ? TRUE : FALSE, 'debug' => TRUE);
        try {
            $c = \Fuel\Core\Ftp::forge($b);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
        try {
            $c->change_dir($a['path']);
        } catch (Exception $ex) {
            return 'The directory ' . $a['path'] . ' does not exist in the FTP server.';
        }

        return 'Ftp server is ready to rock.';
        $c->close();
    }

    /**
     * Returns array
     * pushby
     * avatar_url
     * hash
     * post_data
     * commit_count
     * commit_message
     *
     * @param type $input -> payload.
     * @param type $deploy_id -> deploy to id optional
     */
    public static function parsePayload($input, $deploy_id = NULL) {

        $i = json_decode($input['payload']);
        $service = 'none';
        if (isset($i->canon_url)) {
            if (preg_match('/bitbucket/i', $i->canon_url)) {
                $service = 'bitbucket';
            }
        }

        if (isset($i->repository)) {
            if (isset($i->repository->url)) {
                if (preg_match('/github/i', $i->repository->url)) {
                    $service = 'github';
                }
            }
        }

        utils::log('service: ' . $service);

        if ($service == 'github') {
            $lc = count($i->commits) - 1;
            $branch = $i->ref;
            $branch = explode('/', $branch);
            $branch = $branch[count($branch) - 1];

            return array(
                'user'         => $i->pusher->name,
                'avatar_url'     => $i->sender->avatar_url,
                'hash'           => $i->after,
                'post_data'      => serialize($i),
                'commit_count'   => count($i->commits),
                'commit_message' => $i->commits[$lc]->message,
                'branch'         => $branch
            );
        }

        if ($service == 'bitbucket') {
            $lc = count($i->commits) - 1;

            return array(
                'user'         => $i->commits[$lc]->author,
                'avatar_url'     => '',
                'hash'           => $i->commits[$lc]->raw_node,
                'post_data'      => serialize($i),
                'commit_count'   => count($i->commits),
                'commit_message' => $i->commits[$lc]->message
            );
        }
    }

    public static function humanize_data($bytes) {
        $decimals = 2;
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

    public static function strip_passwords($data) {
        foreach ($data as $k => $v) {
            if (isset($data[$k]['pass'])) {
                if (!empty($data[$k]['pass'])) {
                    $data[$k]['pset'] = TRUE;
                } else {
                    $data[$k]['pset'] = FALSE;
                }
                unset($data[$k]['pass']);
            }
            if (isset($data[$k]['password'])) {
                if (!empty($data[$k]['password'])) {
                    $data[$k]['passwordset'] = TRUE;
                } else {
                    $data[$k]['passwordset'] = FALSE;
                }
                unset($data[$k]['password']);
            }
        }

        return $data;
    }

    public static function log($string) {
        DB::insert('log')->set(array('a' => $string,))->execute();
    }
}

/* end of file auth.php */
