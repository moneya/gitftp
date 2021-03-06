<?php

use Fuel\Core\File;
use Fuel\Core\Input;
use Fuel\Core\Str;
use Gf\Config;
use Gf\Exception\UserException;
use Gf\Project;
use Gf\Record;
use Gf\Utils;

class Controller_Console_Api_Server extends Controller_Console_Authenticate {

    public function post_log_file () {
        try {
            $file = Input::json('filename', false);
            if (!$file)
                throw new UserException('Missing parameters');

            try {
                $contents = File::read(DOCROOT . 'logs/' . $file, true);
            } catch (Exception $e) {
                throw new UserException('The log file does not exists');
            }

            $r = [
                'status' => true,
                'data'   => $contents,
            ];
        } catch (\Exception $e) {
            $e = \Gf\Exception\ExceptionInterceptor::intercept($e);
            $r = [
                'status' => false,
                'reason' => $e->getMessage(),
            ];
        }
        $this->response($r);
    }

    public function post_deploy () {
        try {
            $project_id = Input::json('project_id', false);
            $server_id = Input::json('server_id', false);
            $deploy_type = Input::json('deploy.type', false);
            $target_revision = Input::json('deploy.target_revision', false);

            if (!$project_id or !$server_id or !$deploy_type or !$target_revision)
                throw new UserException('Missing parameters');

            $project = Project::get_one([
                'id' => $project_id,
            ]);
            if (!$project)
                throw new UserException('Project not found');

            $server = \Gf\Server::get_one([
                'id' => $server_id,
            ]);
            if (!$server)
                throw new UserException('Server not found');

            if ($deploy_type != Record::type_fresh_upload and
                $deploy_type != Record::type_revert and
                $deploy_type != Record::type_update
            )
                throw new UserException('Invalid record type');

            $repo_path = Utils::systemDS(DOCROOT . Project::getRepoPath($project_id));
            $gitLocal = \Gf\Git\GitLocal::instance($repo_path);
            $revision = $gitLocal->verifyHash($target_revision);
            if (!$revision)
                throw new UserException("The revision '$target_revision'' was not found in the current repository, if this is was not the expected output please click on the Sync button on the top right corner");

            $hashExists = $gitLocal->hashExistsInBranch($revision, $server['branch']);
            if ($hashExists)
                throw new UserException("The revision '$target_revision' does not exists in '{$server['branch']}', cannot deploy to another branch");

            $commit = $gitLocal->log($revision);
            if (!$commit)
                $commit = [];

            Record::insert([
                'server_id'       => $server_id,
                'project_id'      => $project_id,
                'type'            => $deploy_type,
                'revision'        => $server['revision'],
                'target_revision' => $revision,
                'status'          => Record::status_new,
                'commit'          => json_encode($commit),
            ]);

            Utils::asyncCall('project', $project_id);
//            $timestamp = Utils::timeNow() . Str::random('alnum', 4);
//            Utils::executeTaskInBackground('deploy:project', $project_id, 'logs/' . $timestamp);

            $r = [
                'status' => true,
            ];
        } catch (\Exception $e) {
            $e = \Gf\Exception\ExceptionInterceptor::intercept($e);
            $r = [
                'status' => false,
                'reason' => $e->getMessage(),
            ];
        }
        $this->response($r);
    }


    public function post_compare () {
        try {
            $project_id = Input::json('project_id', false);
            $server_id = Input::json('server_id', false);
            // $source_revision = Input::json('source_revision', false);
            $target_revision = Input::json('target_revision', false);

            if (!$project_id)
                throw new UserException('Missing parameters');

            $project = Project::get_one([
                'id' => $project_id,
            ], [
                'provider',
                'git_name',
                'git_username',
            ]);
            if (!$project)
                throw new UserException('project not found');

            $server = \Gf\Server::get_one([
                'id' => $server_id,
            ], [
                'revision',
                'branch',
            ]);
            if (!$server)
                throw new UserException('Server not found');
            if (!$target_revision)
                $target_revision = $server['branch'];

            if (strtolower($target_revision) == "head")
                throw new UserException("HEAD is ambiguous, please provide a specific revision, branch or tag");

            $source_revision = $server['revision'];

            $repo_path = Utils::systemDS(DOCROOT . Project::getRepoPath($project_id));
            $gitLocal = \Gf\Git\GitLocal::instance($repo_path);

            $revision = $gitLocal->verifyHash($target_revision);
            if (!$revision)
                throw new UserException("The revision '$target_revision'' was not found in the current repository, if this is was not the expected output please click on the Sync button on the top right corner");

            $hashExists = $gitLocal->hashExistsInBranch($revision, $server['branch']);
            if ($hashExists)
                throw new UserException("The revision '$target_revision' does not exists in '{$server['branch']}', cannot deploy to another branch");

            list($files) = $gitLocal->diff($source_revision, $target_revision);
            $commits = $gitLocal->commitsBetween($source_revision, $target_revision);

            $r = [
                'status' => true,
                'data'   => [
                    'files'   => $files,
                    'commits' => $commits,
                ],
            ];
        } catch (\Exception $e) {
            $e = \Gf\Exception\ExceptionInterceptor::intercept($e);
            $r = [
                'status' => false,
                'reason' => $e->getMessage(),
            ];
        }
        $this->response($r);
    }

    public function post_view () {
        try {
            $server_id = Input::json('server_id', false);
            $project_id = Input::json('project_id', false);

            $select = [
                'id',
                'name',
                'project_id',
                'branch',
                'type',
                'secure',
                'host',
                'port',
                'username',
                'path',
                'auto_deploy',
                'created_at',
                'updated_at',
                'revision',
                'key_id',
            ];

            if ($server_id) {
                $servers = \Gf\Server::get_one([
                    'id' => $server_id,
                ], $select);
            } elseif ($project_id) {
                $servers = \Gf\Server::get([
                    'project_id' => $project_id,
                ], $select);
            } else {
                throw new UserException('Missing parameters');
            }

            $r = [
                'status' => true,
                'data'   => $servers,
            ];
        } catch (\Exception $e) {
            $e = \Gf\Exception\ExceptionInterceptor::intercept($e);
            $r = [
                'status' => false,
                'reason' => $e->getMessage(),
            ];
        }
        $this->response($r);
    }

    /**
     * Downloads the key by cache id
     *
     * @param $id
     */
    public function get_download_key ($id) {
        try {
            try {
                $data = \Fuel\Core\Cache::get('keys.' . $id);
                $pu = $data['pu'];
            } catch (Exception $e) {
                throw new UserException('The key cache does not exists, please refresh the page and try again');
            }

            $fileName = Str::random();
            $dir = Utils::systemDS(DOCROOT . 'repositories/keys/temp');

            File::create($dir, $fileName, $pu);
            File::download("$dir/$fileName", 'rsa-public-key.pub');
            File::delete("$dir/$fileName");
            $r = [
                'status' => true,
            ];
        } catch (\Exception $e) {
            $e = \Gf\Exception\ExceptionInterceptor::intercept($e);
            $r = [
                'status' => false,
                'reason' => $e->getMessage(),
            ];
        }

    }

    /**
     * Generate a key pair
     * and send back the cache id of the pair.
     */
    public function post_key () {
        try {
            /**
             * Cache id for retrieving the same key pair
             */
            $id = Input::json('id', false);

            list($id, $pu) = \Gf\Keys::getPair($id);

            $r = [
                'status' => true,
                'data'   => [
                    'id' => $id,
                    'pu' => $pu,
                ],
            ];
        } catch (\Exception $e) {
            $e = \Gf\Exception\ExceptionInterceptor::intercept($e);
            $r = [
                'status' => false,
                'reason' => $e->getMessage(),
            ];
        }
        $this->response($r);
    }

    public function post_delete () {
        try {
            $project_id = Input::json('project_id', false);
            $server_id = Input::json('server_id', false);
            if (!$project_id or !$server_id)
                throw new UserException('Missing parameters');

            Record::remove([
                'server_id'  => $server_id,
                'project_id' => $project_id,
            ]);

            $af = \Gf\Server::remove([
                'id'         => $server_id,
                'project_id' => $project_id,
            ]);

            if (!$af)
                throw new UserException('The server was not found');


            $r = [
                'status' => true,
            ];
        } catch (\Exception $e) {
            $e = \Gf\Exception\ExceptionInterceptor::intercept($e);
            $r = [
                'status' => false,
                'reason' => $e->getMessage(),
            ];
        }
        $this->response($r);
    }

    public function post_create () {
        try {
            $name = Input::json('server.name', false);
            $branch = Input::json('server.branch', false);
            $auto_deploy = Input::json('server.auto_deploy', false);
            $type = Input::json('server.type', false);
            $host = Input::json('server.host', false);
            $port = Input::json('server.port', false);
            $username = Input::json('server.username', false);
            $password = Input::json('server.password', false);
            $path = Input::json('server.path', '');
            $secure = Input::json('server.secure', false);
            $project_id = Input::json('project_id', false);
            $edit_password = Input::json('server.edit_password', false);
            $id = Input::json('server.id', false);
            $key_id = Input::json('server.key_id', false);

            if (!$name or !$branch or !$type or !$project_id)
                throw new UserException('Missing parameters');

            if ($type == \Gf\Server::type_ftp or $type == \Gf\Server::type_sftp) {
                if (!$host or !$port or !$username or !$project_id)
                    throw new UserException('Missing parameters');
                if (!$id and !$password)
                    throw new UserException('Missing parameters');
            }

            $set = [
                'name'        => $name,
                'project_id'  => $project_id,
                'branch'      => $branch,
                'type'        => $type,
                'path'        => $path,
                'added_by'    => $this->user_id,
                'auto_deploy' => !!$auto_deploy,
            ];

            if ($type == \Gf\Server::type_ftp) {
                $set['secure'] = !!$secure;
                $set['host'] = $host;
                $set['port'] = $port;
                $set['username'] = $username;

                if ($edit_password)
                    $set['password'] = $password;
            }
            if ($type == \Gf\Server::type_sftp) {
                $set['secure'] = 0;
                $set['host'] = $host;
                $set['port'] = $port;
                $set['username'] = $username;
                if ($edit_password) {
                    $set['password'] = $password;
                }
                if ($key_id) {
                    $key = \Gf\Keys::getById($key_id);
                    if (!$key)
                        throw new UserException('The key id is not valid');

                    $set['key_id'] = $key_id;
                } else {
                    $set['key_id'] = null;
                }
            }

            if ($type == \Gf\Server::type_local) {
                $set['password'] = null;
                $set['secure'] = 0;
                $set['host'] = null;
                $set['port'] = null;
                $set['username'] = null;
            }


            if ($id) {
                $server_id = \Gf\Server::update([
                    'id' => $id,
                ], $set);
            } else {
                $server_id = \Gf\Server::insert($set);
            }


            $r = [
                'status' => true,
                'data'   => $server_id,
            ];
        } catch (\Exception $e) {
            $e = \Gf\Exception\ExceptionInterceptor::intercept($e);
            $r = [
                'status' => false,
                'reason' => $e->getMessage(),
            ];
        }
        $this->response($r);
    }


    public function post_test () {
        try {
            $server = Input::json('server');
            $writeTest = Input::json('writeTest', true);

            if ($server['type'] == \Gf\Server::type_ftp or $server['type'] == \Gf\Server::type_sftp) {
                if (!$server['edit_password'] and $server['id']) {
                    $ser = \Gf\Server::get_one([
                        'id' => $server['id'],
                    ], ['password']);
                    if (!$ser)
                        throw new UserException('The server does not exists');
                    $server['password'] = $ser['password'];
                }
            }

            if ($server['type'] == \Gf\Server::type_sftp and isset($server['key_id']) and $server['key_id']) {
                $server['password'] = '';
            }

            if (!isset($server['path']) or !$server['path'])
                $server['path'] = '';

            $connection = \Gf\Deploy\Connection::instance($server)->connection();

            try {
                $contents = $connection->listContents();

                $empty = true;
                if (count($contents))
                    $empty = false;

                if ($writeTest) {
                    $s = $connection->put('gitftp_test_file.txt', 'This is a test file, its safe to delete it.');
                    if (!$s)
                        throw new UserException('Could not write to Deploy path, please grant permissions');

                    $s = $connection->delete('gitftp_test_file.txt');
                    if (!$s)
                        throw new UserException('Could not delete test file from deploy path, please grant permissions');
                }
            } catch (Exception $e) {
                throw new UserException($e->getMessage());
            }

            $r = [
                'status' => true,
                'data'   => [
                    'empty'       => $empty,
                    'directories' => $contents,
                ],
            ];
        } catch (\Exception $e) {
            $e = \Gf\Exception\ExceptionInterceptor::intercept($e);
            $r = [
                'status' => false,
                'reason' => $e->getMessage(),
            ];
        }
        $this->response($r);
    }
}
