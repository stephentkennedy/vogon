<?php
/***
 * This is a query handler for the db_handler class and its extensions.
 * It's purpose is to allow the class to be easily adapted to other database drivers such as the WordPress WPDB class.
 ***/
class db_query {
    private $mode = 'thumb';
    private $db = false;
    public $error = false;
    public $last = false;

    public function __construct($db = false){
        if($db == false){
            global $db;
            if(empty($db)){
                return die('instantiated without database object in $db variable.');
            }

            if(
                class_exists('thumb')
                && $db instanceof thumb
            ){
                $this->mode = 'thumb';
            }else if($db instanceof PDO){
                $this->mode = 'PDO';
            }else if(
                class_exists('wpdb')
                && $db instanceof wpdb
            ){
                $this->mode = 'wpdb';
            }else{
                return die('instantiated without compatable database object in $db variable.');
            }

            $this->db = $db;
        }
    }

    public function add_param($param, $column, $params){
        $token = '';

        switch($this->get_mode()){
            case 'PDO':
            case 'thumb':
                $token = $this->pdo_token($param, $column);
                $params = $this->pdo_add_to_params($token, $param, $params);
                break;
            case 'wpdb':
                $token = $this->wpdb_token($param, $column);
                $params = $this->wpdb_add_to_params($token, $param, $params);
                break;
        }

        $return = [
            'token' => $token,
            'params' => $params
        ];
        return $return;
    }

    public function t_query($sql, $params = []){
        return $this->query($sql, $params);
    }

    public function query($sql, $params = []){
        $return = false;
        switch($this->get_mode()){
            case 'thumb':
                $return = $this->thumb_query($sql, $params);
                break;
            case 'PDO':
                $return = $this->pdo_query($sql, $params);
            case 'wpdb':
                $return = $this->wpdb_query($sql, $params);
        }
    }

    public function get_mode(){
        return $this->mode;
    }

    private function pdo_token($param, $column){
        return ':'.$column['machine_name'];
    }

    private function pdo_add_to_params($token, $param, $params){
        if(isset($params[$token])){
            $token .= '_1';
            return $this->pdo_add_to_params($token, $param, $params);
        }
        $params[$token] = $param;

        return $params;
    }

    private function wpdb_add_to_params($token, $param, $params){
        $params[] = $param;
        return $params;
    }

    private function wpdb_token($param, $column){
        if(is_int($param)){
            $token = '%d';
        }else if(is_numeric($param)){
            $token = '%f';
        }else{
            $token = '%s';
        }

        return $token;
    }

    private function thumb_query($sql, $params){
        $check = $this->db->t_query($sql, $params);
        if($check === false){
            $this->error = $this->db->error;
            return false;
        }else{
            $this->last = $this->db->last;
        }
        return $this->pdo_query_return_process($sql, $check);
    }

    private function pdo_query($sql, $params){
        $this->db->beginTransaction();
        $trans = $this->db->prepare($sql);
        try{
            $trans->execute($params);
            $this->last = $this->db->lastInsertId();
            $this->db->commit();
            return $this->pdo_query_return_process($sql, $trans);
        }catch(PDOException $e){
            $this->db->rollBack();
            $this->error = $e;
            return false;
        }
    }

    private function pdo_query_return_process($sql, $returned){
        $return = false;
        $type = $this->query_type($sql);
        if($returned === false){
            return $return;
        }
        switch($type){
            case 'SELECT':
                $return = $returned->fetchAll();
                break;
            case 'UPDATE':
            case 'REPLACE':
            case 'DELETE':
                $return = true;
                break;
            case 'INSERT':
                $return = $this->last;
                break;
        }

        return $return;
    }

    private function wpdb_query($sql, $params){
        $sql = $this->db->prepare($sql, $params);
        switch($this->query_type($sql)){
            case 'SELECT':
                $return = $this->db->get_results($sql, ARRAY_A);
                break;
            case 'UPDATE':
            case 'REPLACE':
            case 'DELETE':
                $return = $this->db->query($sql);
                break;
            case 'INSERT':
                $this->db->query($sql);
                $return = $this->db->get_var('SELECT LAST_INSERT_ID()');
                break;
        }
        return $return;
    }

    private function query_type($sql){
        $possible = [
            'SELECT',
            'UPDATE',
            'INSERT',
            'DELETE',
            'REPLACE'
        ];

        $test_sql = strtolower($sql);

        foreach($possible as $type){
            $test_type = strtolower($type);
            $test_len = strlen($test_type);
            $test_str = substr($test_sql, 0, $test_len);
            if($test_str == $test_type){
                return $type;
            }
        }
        return false;
    }
}