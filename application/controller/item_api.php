<?php
defined('APPPATH') OR exit('No direct script access allowed');

class Item_api extends Controller
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

    public function addItem()
    {
        $rParam = Array();
        if(!isset($_POST["type"])) {
            $this->response(false, array("message"=>"잘못된 접근입니다."));
            exit;
        }

        $rParam["user_id"] = $_SESSION['user']['UserID'];
        foreach ($_POST as $key => $values) {
            $rParam[$key] = $values;

            if(!$values) {
                $this->_bResult = false;
                $this->rData["message"] = "빈칸을 모두 채워주세요.";
            }
        }

        if($this->_bResult === true) {

            $item_model = $this->loadModel('item_model');
            $bRtID = $item_model->insertDBData($rParam,"trade_item", true);

            if($bRtID) {
                if(isset($_FILES["imageUpload"])) {
                    for ($i = 0; $i < count($_FILES["imageUpload"]["name"]); $i++) {

                        if (!$_FILES["imageUpload"]["name"][$i]) {
                            continue;
                        } else {
                            $rFiles[$i]["name"] = $_FILES["imageUpload"]["name"][$i];
                            $rFiles[$i]["type"] = $_FILES["imageUpload"]["type"][$i];
                            $rFiles[$i]["tmp_name"] = $_FILES["imageUpload"]["tmp_name"][$i];
                            $rFiles[$i]["error"] = $_FILES["imageUpload"]["error"][$i];
                            $rFiles[$i]["size"] = $_FILES["imageUpload"]["size"][$i];
                        }
                    }

                    foreach ($rFiles as $rFile) {
                        $bFileReturn = $this->imageUpload($rParam["type"], $rFile);

                        if ($bFileReturn["success"] === true) {
                            $bFileReturn["dbData"]["item_serial"] = $bRtID;
                            $bRtFileID = $item_model->insertDBData($bFileReturn["dbData"], "trade_item_image", true);

                            if (!$bRtFileID) {
                                $filePath = $bFileReturn["dbData"]["file_dir"] . "/" . $bFileReturn["dbData"]["save_file_name"];
                                $this->fileDel($filePath);

                                $this->bResult = false;
                                $this->rData["message"] = "판매등록에 실패하였습니다. [Error-03]";
                            }
                        } else {
                            $this->bResult = false;
                            $this->rData["message"] = $bFileReturn["error"];
                        }
                    }
                }
            } else {
                $this->bResult = false;
                $this->rData["message"] = "판매등록에 실패하였습니다. [Error-01]";
                $this->rData["serial"] = $bRtID;
            }
        }

        $this->response($this->_bResult, $this->rData);
    }

    protected function getOfferCount($model, $rParam)
    {
        return $model->getDBData(0, 0, "trade_offer", $rParam, true);
    }

    public function getOfferData()
    {
        if(!isset($_POST["item_serial"])) {
            $this->response(false, array("message"=>"잘못된 접근입니다."));
            exit;
        }

        $rParam = array();
        $rParam["item_serial"] = $_POST["item_serial"];

        $item_model = $this->loadModel('item_model');
        $rOfferDB = $item_model->getDBData(0, 0, "trade_offer", $rParam);

        if(!$rOfferDB) {
            $this->_bResult = false;
        }
        $this->rData = $rOfferDB;

        $this->response($this->_bResult, $this->rData);
    }

    public function tradeOffer($bIsCnt = false)
    {

        if(!isset($_POST["item_serial"])) {
            $this->response(false, array("message"=>"잘못된 접근입니다."));
            exit;
        }

        $trade_model = $this->loadModel('trade_model');
        $rUserDB = $trade_model->userData($_SESSION["user"]["UserID"], array("serial", "mileage"));

        if(isset($_POST["offer_cost"])) {
            if($rUserDB[0]["mileage"] < $_POST["offer_cost"]) {
                $this->response(false, array("message"=>"M"));
                exit;
            }
        }

        $rWhereData = array();
        $rWhereData["item_serial"] = $_POST["item_serial"];
        $rWhereData["offer_id"] = $_SESSION["user"]["UserID"];

        $item_model = $this->loadModel('item_model');
        if($bIsCnt== true) {
            $nOfferCnt = $this->getOfferCount($item_model, $rWhereData);

            if($nOfferCnt > 0) {
                $this->_bResult= false;
            }
        } else {
            $confirmData = array();

            if($_POST["mode"] == "update") {
                $confirmData["offer_cost"] = $_POST["offer_cost"];
                $confirmData["approve_yn"] = "D";

                foreach ($confirmData as $key=>$item) {
                    if(!$item) {
                        $this->_bResult = false;
                        $this->rData["message"] = "파라미터 오류.";
                    }
                }

                if($this->_bResult === true) {
                    $bRtID = $item_model->updateDBData($confirmData,"trade_offer",$rWhereData);

                    if(!$bRtID) {
                        $this->_bResult = false;
                        $this->rData["message"] = "거래 제안에 실패하였습니다.";
                    }
                }

            } else {
                $confirmData["item_serial"] = $rWhereData["item_serial"];
                $confirmData["offer_id"] = $rWhereData["offer_id"];
                $confirmData["offer_cost"] = $_POST["offer_cost"];
                $confirmData["approve_yn"] = "D";

                foreach ($confirmData as $key=>$item) {
                    if(!$item) {
                        $this->_bResult = false;
                        $this->rData["message"] = "파라미터 오류.";
                    }
                }

                if($this->_bResult === true) {
                    $bRtID = $item_model->insertDBData($confirmData,"trade_offer", true);

                    if(!$bRtID) {
                        $this->_bResult = false;
                        $this->rData["message"] = "거래 제안에 실패하였습니다.";
                    }
                }
            }
        }

        $this->response($this->_bResult, $this->rData);
    }


    public function itemDelete()
    {
        if(!$_POST["serial"]) {
            $this->response(false, array("message"=>"잘못된 접근입니다."));
            exit;
        }

        $item_model = $this->loadModel('item_model');

        $rWhereData = array();
        $rWhereData["item_serial"] = $_POST["serial"];
        $offerCnt = $this->getOfferCount($item_model, $rWhereData);

        if($offerCnt > 0) {
            $this->_bResult = false;
            $this->rData["message"] = "제안된 거래가 있는 경우 삭제할 수 없습니다.";
        }

        if($this->_bResult === true) {
            if (!$item_model->deleteDBData("trade_item", array("serial"=>$_POST["serial"]))) {
                $this->_bResult = false;
                $this->rData["message"] = "데이터 삭제에 실패했습니다.";
            }
        }

        $this->response($this->_bResult, $this->rData);
    }

    public function getRatingAvg()
    {

        $rWhereData = array();
        $rWhereData["rate_id"] = $_POST["rate_id"];

        $item_model = $this->loadModel('item_model');
        $rRatingDB  = $item_model->getDBData(0, 0, "rating", $rWhereData);

        if(count($rRatingDB) == 0){
            $rate_times = 0;
            $rate_value = 0;
            $rate_bg = 0;
        }else{
            foreach($rRatingDB as $result){
                $rate_db[] = $result;
                $sum_rates[] = $result['rate'];
            }
            $rate_times = count($rate_db);
            $sum_rates = array_sum($sum_rates);
            $rate_value = round($sum_rates/$rate_times, 2);
        }

        $this->response($this->_bResult, array("rateAvg" => $rate_value));
    }

    protected function imageUpload($uploadPath, $rParam)
    {
        $mainPath = "./uploads/";
        $filePath = $uploadPath."/".date("m");

        if(!is_dir($mainPath.$filePath)){
            mkdir($mainPath.$filePath, 0777);
        }

        $valid_formats = array("jpg", "jpeg", "png", "gif", "bmp");

        $data   = array();
        $data["success"] = false;

        $name = $rParam["name"];
        $size = $rParam["size"];

        if(strlen($name))
        {
            list($txt, $ext) = explode(".", $name);
            if(in_array($ext,$valid_formats))
            {
                if($size<(1024*1024)) // Image size max 1 MB
                {
                    $saveFileName = $this->randomString();
                    $actual_image_name = time()."_".$saveFileName.".".$ext;
                    $tmp = $rParam["tmp_name"];

                    if(move_uploaded_file($tmp, $mainPath.$filePath."/".$actual_image_name))
                    {
                        $data["success"] = true;
                        $data["dbData"] = array("file_dir"=>$filePath, "save_file_name"=>$actual_image_name, "origin_file_name"=>$name, "extention"=>$ext);
                    }
                    else
                    {
                        $data["success"] = false;
                        $data["error"] = "error";
                    }
                } else {
                    $data["error"] = "이미지 사이즈를 줄여주세요.";
                }
            } else {
                $data["error"] = "이미지파일만 업로드 가능합니다.";
            }
        }

        return $data;
    }

    protected function fileDel($path)
    {
        $basePath = "./uploads/";

        if(file_exists($basePath.$path)) {
            unlink($path);
        }

        return;
    }

    protected function randomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        $charactersLength = strlen($characters);
        $randomString = '';

        for($i=0; $i<$length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }
}