<?php

class Model_Deploy extends Model {

    private $table = 'deploy';
    private $user_id;

    public function __construct() {
        if (Auth::check()) {
            $this->user_id = Auth::get_user_id()[1];
        } else {
            return false;
        }
    }

    public function get($id = null, $select = array()) {

        if (count($select) == 0) {
            $q = DB::select();
        } else {
            $q = DB::select_array($select);
        }

        $q = $q->from($this->table)
                ->where('user_id', $this->user_id);

        if ($id != null) {
            $q = $q->and_where('id', $id);
        }

        $a = $q->execute()->as_array();

        foreach ($a as $k => $v) {

            if (isset($v['ftp'])) {
                $a[$k]['ftp'] = unserialize($v['ftp']);
            }
//            $a[$k]['lastdeploy'] = Date::forge($a[$k]['lastdeploy'])->format("%m/%d/%Y %H:%M");
        }

        return $a;
    }

    public function set($id, $set = array(), $direct = false) {

        if (!$direct) {
            $a = DB::select()->from($this->table)->where('id', $id)->execute()->as_array();

            if (empty($a) or $a[0]['user_id'] != $this->user_id) {
                return false;
            }
        }

        return DB::update($this->table)->set($set)->where('id', $id)->execute();
    }

    public function delete($id) {
        $a = DB::select()->from($this->table)->where('id', $id)->execute()->as_array();

        if (empty($a) or $a[0]['user_id'] != $this->user_id) {
            return false;
        }

        return DB::delete($his->table)->where('id', $id)->execute();
    }

}
