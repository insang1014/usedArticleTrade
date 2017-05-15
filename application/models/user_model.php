<?php
defined('APPPATH') OR exit('No direct script access allowed');

class User_model extends Model
{
    private $_sDBName	= "item_trade";
    private $_rTable	= array();
    private $_rColumn	= array();
    private $db;

    public function __construct($db) {
        try {
            $this->db = $db;
        } catch (PDOException $e) {
            exit('데이터베이스 연결에 오류가 발생했습니다.');
        }

        $this->_rColumn["users"] = "user_id, user_pwd, name, birthday, postcode, roadaddr, addr";
        $this->_rColumn["mileage_charge"] = "user_id, charge_cost, charge_type, status";
        $this->_rColumn["rating"] = "item_serial, rate_id, user_id, rate, memo";
        $this->_rTable = array_keys($this->_rColumn);
    }

    public function getDBData($nOffset=0, $nRowcnt=0, $sTable, $rWhereData=array(), $bGetTotCnt=false) {

        $query = "SELECT * FROM {$this->_sDBName}.".$sTable;

        $rCombine = $this->queryCombine($rWhereData);

        if($rCombine) {
            $query .= " WHERE 1=1 ";

            foreach ($rCombine["where"] as $key => $item) {
                $query .= $item;
            }
        }

        if($nRowcnt > 0) {
            $query .= " LIMIT ".$nOffset.", ".$nRowcnt;
        }
        $stmt = $this->db->prepare($query);

        print_r($rCombine["execute"]);
        foreach ($rCombine["execute"] as $key => $val) {
            $stmt->bindParam($key, $val);
        }
        $stmt->execute();

        if($bGetTotCnt === true) {
            return $stmt->rowCount();
        }

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    /**
     * @desc : (DB) Update
     **/
    public function mileageUpdate($rParam, $rWhereColumn)
    {
        foreach ($rWhereColumn as $item) {
            if(!$item) {
                return false;
            }
        }

        $query = "UPDATE {$this->_sDBName}.users SET mileage = (mileage + :charge_cost) ";

        $rCombine = $this->queryCombine($rWhereColumn);
        if($rCombine) {
            $query .= " WHERE 1=1 ";

            foreach ($rCombine["where"] as $key => $item) {
                $query .= $item;
            }

            $rParam = array_merge($rParam, $rCombine["execute"]);
        }
        $stmt = $this->db->prepare($query);
        try {
            $this->db->beginTransaction();
            $stmt->execute($rParam);
            $this->db->commit();
        } catch(\PDOExecption $e) {
            $this->db->rollback();
        }
        return ($stmt) ? true : false;
    }

    public function insertDBData($rParam, $sTable, $bRtID=false)
    {
        if (!in_array($sTable, $this->_rTable)) {
            return false;
        }

        $params = array();
        $rColumnKey = explode(",", preg_replace("/\s+/", "", $this->_rColumn[$sTable]));

        $setColumn = array();
        foreach ($rColumnKey as $key => $item) {
            if(isset($rParam[$item])) {
                if($rParam[$item]) {
                    $rColumnKey[$key] = ":".$item;
                    $params[$item] = $rParam[$item];
                }
            } else {
                return false;
            }
        }
        $setColumn = implode(",", $rColumnKey);

        $query = "INSERT INTO {$this->_sDBName}.".$sTable.
            " ( " . $this->_rColumn[$sTable] . " ) " .
            " VALUES ( ". $setColumn . " )";

        $stmt = $this->db->prepare($query);

        try {
            $this->db->beginTransaction();
            foreach ($rParam as $key => $val) {
                $stmt->bindParam($key, $val);
            }
            $stmt->execute();
            $insert_id = $this->db->lastInsertId();
            $this->db->commit();

            if ($bRtID === true) {
                return $insert_id;
            }

        } catch(\PDOExecption $e) {
            $this->db->rollback();
        }

        return ($stmt) ? true : false;
    }
}
