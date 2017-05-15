<?php
defined('APPPATH') OR exit('No direct script access allowed');

class Trade_api extends Controller
{
    private $_bResult = true;
    private $rData = array();

    public function __construct()
    {
        parent::__construct();

        if($_SERVER["REQUEST_METHOD"] != "POST") {
            exit('No direct script access allowed');
        }
    }

    public function approveTrade()
    {

        if(!isset($_POST["type"]) || !isset($_POST["itemSerial"])) {
            $this->response(false, array("message"=>"잘못된 접근입니다."));
            exit;
        }

        $rParam = array();
        $rParam["item_serial"] = $_POST["itemSerial"];
        $rParam["offer_serial"] = $_POST["offerSerial"];
        $rParam["user_id"] = $_POST["user_id"];
        $rParam["offer_id"] = $_POST["offer_id"];
        $rParam["cost"] = $_POST["cost"];
        $rParam["type"] = $_POST["type"];
        $rParam["status"] = "Y";

        foreach ($rParam as $item) {
            if(!$item) {
                $this->_bResult = false;
                $this->rData["message"] = "파라미터 오류";
            }
        }

        if($this->_bResult === true) {
            $trade_model = $this->loadModel('trade_model');
            $bRtID = $trade_model->insertDBData($rParam, "approve_trade", true);

            if (!$bRtID) {
                $this->_bResult = false;
            } else {
                $trade_model->updateDBData(array("status" => "I"), "trade_item", array("serial" => $rParam["item_serial"]));
                $trade_model->updateDBData(array("approve_yn" => "Y"), "trade_offer", array("serial" => $rParam["offer_serial"]));
            }
        }

        $this->response($this->_bResult, $this->rData);
    }

    public function closeItemTrade()
    {

        if(!isset($_POST["type"]) || !isset($_POST["item_serial"])) {
            $this->response(false, array("message"=>"잘못된 접근입니다."));
            exit;
        }

        $rWhereData = array();
        $rWhereData["type"] = $_POST["type"];
        $rWhereData["user_id"] = $_POST["user_id"];
        $rWhereData["item_serial"] = $_POST["item_serial"];

        foreach ($rWhereData as $item) {
            if(!$item) {
                $this->_bResult = false;
                $this->rData["message"] = "파라미터 오류";
            }
        }

        $trade_model = $this->loadModel('trade_model');
        $rApproveDB  = $trade_model->getDBData(0, 1, "approve_trade", $rWhereData);

        if(count($rApproveDB) < 0)
        {
            $this->_bResult = false;
            $this->rData["message"] = "진행중인 거래내역이 없습니다.";
        }

        if($this->_bResult === true) {
            $rUserDB = $trade_model->userData($rApproveDB[0]["user_id"], array("serial", "mileage"));
            $rOfferDB = $trade_model->userData($rApproveDB[0]["offer_id"], array("serial", "mileage"));

            $nCost = $rApproveDB[0]["cost"];
            $userMileage = $rUserDB[0]["mileage"];
            $offerMileage = $rOfferDB[0]["mileage"];

            if ($rApproveDB[0]["type"] == "S") {
                $userMileage = ($userMileage + $nCost);
                $offerMileage = ($offerMileage - $nCost);
            } else {
                $userMileage = ($userMileage - $nCost);
                $offerMileage = ($offerMileage + $nCost);
            }

            try {
                if(!$trade_model->updateDBData(array("status" => "C"), "trade_item", array("serial" => $rApproveDB[0]["item_serial"]))) {
                    throw new Exception("trade_item Close Error",  1);
                }

                if(!$trade_model->updateDBData(array("mileage" => $userMileage), "users", array("serial" => $rUserDB[0]["serial"]))) {
                    throw new Exception("User Mileage Error",  2);
                }

                if(!$trade_model->updateDBData(array("mileage" => $offerMileage), "users", array("serial" => $rOfferDB[0]["serial"]))) {
                    throw new Exception("Offer Mileage Error",  3);
                }

                if(!$trade_model->updateDBData(array("status" => "N"), "approve_trade", array("serial" => $rApproveDB[0]["serial"]))) {
                    throw new Exception("Approve Status Error",  4);
                }
            } catch(Exception $e) {
                $this->_bResult = false;
                $this->rData["message"] = $e->getMessage() . "(ErrorCode : " . $e->getCode() . ")";
            }

        }

        $this->response($this->_bResult, $this->rData);
    }
}