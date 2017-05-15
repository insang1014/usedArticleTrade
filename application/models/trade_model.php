<?php
defined('APPPATH') OR exit('No direct script access allowed');

class Trade_model extends Model
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

        $this->_rColumn["trade_item"] = "user_id, type, title, contents, target_cost, status";
        $this->_rColumn["trade_item_image"] = "item_serial, file_dir, save_file_name, origin_file_name, extention";
        $this->_rColumn["trade_offer"] = "item_serial, offer_id, offer_cost, approve_yn";
        $this->_rColumn["approve_trade"] = "item_serial, user_id, offer_id, cost, type,  status";
        $this->_rTable = array_keys($this->_rColumn);
    }

    public function userData($user_id, $rColumn=array()) {

        $sCoulumn = "*";
        if(count($rColumn) > 0) {
            $sCoulumn = implode(",", $rColumn);
        }

        $stmt = $this->db->prepare("SELECT ".$sCoulumn." FROM {$this->_sDBName}.users WHERE user_id = :user_id");
        $stmt->bindValue(":user_id", $user_id);
        $stmt->execute();
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return $results;
    }

    public function getTradeData($rWhereData=array()) {

        $query =" SELECT t_i.*, t_o.serial AS t_o_serial, item_serial, offer_id, offer_cost, approve_yn ";
        $query.=" FROM {$this->_sDBName}.trade_item AS t_i, {$this->_sDBName}.trade_offer AS t_o ";
        $query.=" WHERE t_i.serial = t_o.item_serial ";

        foreach ($rWhereData as $key=>$item) {
            $query.= " AND ".$key."= :".$key;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($rWhereData);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }

    public function getDBData($nOffset=0, $nRowcnt=0, $sTable, $rWhereData=array(), $bGetTotCnt=false, $rUnsetParam = array()) {

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
        foreach ($rCombine["execute"] as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();

        if($bGetTotCnt === true) {
            return $stmt->rowCount();
        }

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(count($rUnsetParam) > 0) {
            foreach ($results as $key=>$value) {
                foreach ($value as $search=>$item) {
                    if(in_array($search, $rUnsetParam)) {
                        unset($results[$key][$search]);
                    }
                }
            }
        }

        return $results;
    }

    /**
     * @desc : (DB) Update
     **/
    public function updateDBData($rParam, $sTable, $rWhereColumn)
    {
        $query = "UPDATE {$this->_sDBName}.".$sTable. " SET ";
        $i=1;
        foreach ($rParam as $key=>$item)
        {
            $query .= $key. " = :".$key;
            if($i < count($rParam)) {
                $query .= ",";
            }
            $i++;
        }

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
            foreach ($rParam as $key => $val) {
                $val = htmlspecialchars($val);
                $stmt->bindValue($key, $val);
            }
            $stmt->execute();
            $this->db->commit();
        } catch(\PDOExecption $e) {
            $this->db->rollback();
        }
        return ($stmt) ? true : false;
    }

    /**
     * @desc : (DB) Insert
     **/
    public function insertDBData($rParam, $sTable, $bRtID=false)
    {
        if (!in_array($sTable, $this->_rTable)) {
            return false;
        }

        $params = array();
        $rColumnKey = explode(",", preg_replace("/\s+/", "", $this->_rColumn[$sTable]));

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
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
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
