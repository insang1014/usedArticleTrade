<?php
defined('APPPATH') OR exit('No direct script access allowed');

class User_api extends Controller
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

    public function userLogin()
    {

        $rWhereData	= array();
        $rWhereData["user_id"] = $_POST["user_id"];

        // DB
        $rUserData = array();
        $user_model = $this->loadModel('user_model');
        $rDB = $user_model->getDBData(0, 1, "users", $rWhereData);
        if($rDB) $rUserData = $rDB[0];

        if($rUserData) {
            if( $_POST["user_id"] == $rUserData["user_id"] && password_verify($_POST["user_pwd"], $rUserData["user_pwd"]) ) {
                $_SESSION['user']['LoginChk'] = "LOGIN";
                $_SESSION['user']['Serial'] = $rUserData["serial"];
                $_SESSION['user']['UserID'] = $rUserData["user_id"];
                $_SESSION['user']['UserName'] = $rUserData["name"];
                $_SESSION['user']['mileage'] = $rUserData["mileage"];
            } else {
                $this->_bResult = false;
                $this->rData["message"] = "아이디 또는 비밀번호를 다시 확인하세요.";
            }
        } else {
            $this->_bResult = false;
            $this->rData["message"] = "아이디 또는 비밀번호를 다시 확인하세요.";
        }
        $this->response($this->_bResult, $this->rData);
    }

    public function userRegister()
    {

        $rParam = Array();
        $confirmData = Array();

        if(!isset($_POST["param"])) {
            $this->response(false, array("message"=>"잘못된 접근입니다."));
            exit;
        }

        foreach ($_POST["param"] as $values) {
            if(!$values["value"]) {
                $this->_bResult = false;
                $this->rData["massage"] = "빈칸을 모두 채워주세요.";
            }

            $rParam[$values["name"]] = $values["value"];
        }

        if($this->_bResult === true) {
            foreach ($rParam as $key => $param) {
                if (!empty($param)) {
                    if ($key == "user_pwd") {
                        $param = password_hash($param, PASSWORD_BCRYPT);
                    }
                    $confirmData[$key] = $param;
                } else {
                    $this->_bResult = false;
                }
            }

            $user_model = $this->loadModel('user_model');
            $bRtID = $user_model->insertDBData($confirmData,"users", true);

            if(!$bRtID) {
                $this->bResult = false;
                $this->rData["massage"] = "계정등록에 실패하였습니다.";
            }
        }

        $this->response($this->_bResult, $this->rData);
    }

    public function idOverlapCheck()
    {

        $rWhereData	= array();
        $rWhereData["user_id"] = $_POST["user_id"];

        $user_model = $this->loadModel('user_model');
        $rUserData	= $user_model->getDBData(0, 0, "users", $rWhereData, true);

        if($rUserData > 0) {
            $this->_bResult = false;
        }

        // TRUE : 사용가능 |  FALSE : 사용불가
        $this->response($this->_bResult);
    }

    public function mileageCharge()
    {
        if(!isset($_POST["charge_cost"]) || !isset($_POST["charge_type"])) {
            $this->response(false, array("message"=>"잘못된 접근입니다."));
            exit;
        }

        $rParam = array();
        $rParam["user_id"] = $_SESSION["user"]["UserID"];
        $rParam["charge_cost"] = $_POST["charge_cost"];
        $rParam["charge_type"] = $_POST["charge_type"];

        // 승인여부
        $rParam["status"] = "Y";

        foreach ($rParam as $item) {
            if(!$item) {
                $this->_bResult = false;
                $this->rData["massage"] = "파라미터 오류.";
            }
        }
        if($this->_bResult === true) {

            $user_model = $this->loadModel('user_model');
            $bRtID = $user_model->insertDBData($rParam,"mileage_charge", true);

            if(!$bRtID) {
                $this->_bResult = false;
                $this->rData["massage"] = "마일리지를 충전할 수 없습니다.\n\n관리자에게 문의하세요.";
            } else {

                $rWhereData = array();
                $rWhereData["serial"] = $bRtID;
                $rWhereData = array_merge($rWhereData, $rParam);
                $rWhereData["status"] = "Y";

                $isCnt = $user_model->getDBData($nOffset=0, $nRowcnt=0, "mileage_charge", $rWhereData, true);

                if($isCnt > 0) {
                    $user_model->mileageUpdate(array("charge_cost" => $rParam["charge_cost"]), array("user_id" => $rParam["user_id"]));
                }
            }
        }

        $this->response($this->_bResult, $this->rData);
    }

    public function traderRating()
    {
        if(!isset($_POST["item_serial"]) || !isset($_POST["rate_id"])) {
            $this->response(false, array("message"=>"잘못된 접근입니다."));
            exit;
        }

        $rParam = array();
        $rParam["user_id"] = $_SESSION["user"]["UserID"];
        $rParam["item_serial"] = $_POST["item_serial"];
        $rParam["rate_id"] = $_POST["rate_id"];
        $rParam["rate"] = $_POST["rate"];
        $rParam["memo"] = $_POST["memo"];

        foreach ($rParam as $item)
        {
            if(!$item) {
                $this->_bResult = false;
                $this->rData["massage"] = "파라미터 오류.";
            }
        }

        $rWhereData = $rParam;
        unset($rWhereData["rate"]);
        unset($rWhereData["memo"]);
        $user_model = $this->loadModel('user_model');
        $rRateDB = $user_model->getDBData(0, 0, "rating", $rWhereData, true);

        if($rRateDB < 1 && $this->_bResult === true){
            $bRtID = $user_model->insertDBData($rParam,"rating", true);

            if(!$bRtID) {
                $this->_bResult = false;
                $this->rData["message"] = "고객평가에 실패하였습니다.";
            }
        }

        $this->response($this->_bResult, $this->rData);
    }
}