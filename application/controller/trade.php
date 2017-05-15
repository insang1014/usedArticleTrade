<?php
defined('APPPATH') OR exit('No direct script access allowed');

class Trade extends Controller
{
    private $_rCommData = array();	// 공통 데이터
    private $data;

    public function __construct()
    {
        parent::__construct();

        // 세선 로그인 검증
        $this->session_validation();

        // 공통 데이터
        $this->_rCommData = array(
            "rCss" => array("local/trade"),
            "rScript" => array("local/common")
        );
    }

    public function index()
    {
        $this->tradeConfirm();
    }

    public function tradeConfirm()
    {

        $rWhereData	= array();
        $rWhereData["item_serial"] = $_GET["item_serial"];
        $rWhereData["type"] = $_GET["mode"];

        // DB
        $trade_model = $this->loadModel("trade_model");
        $rTradeDB	= $trade_model->getTradeData($rWhereData);
        $rTradeData = $rTradeDB[0];

        $rMyDB = $trade_model->userData($rTradeData["user_id"]);
        $rUserDB = $trade_model->userData($rTradeData["offer_id"]);
        $rTradeData["myData"] = $rMyDB[0];
        $rTradeData["traderData"] = $rUserDB[0];

        $this->data = $this->_rCommData;
        $this->data["rScript"] = array_merge($this->data["rScript"], array("local/trade", "rating"));
        $this->data["rDBList"] = $rTradeData;

        $this->views("common/header", $this->data);
        $this->views("trade/confirm", $this->data);
        $this->views("common/footer", $this->data);
    }

    public function TraderRating()
    {

        $rWhereData	= array();
        $rWhereData["item_serial"] = $_GET["item_serial"];
        $rWhereData["type"] = $_GET["mode"];

        // DB
        $trade_model = $this->loadModel("trade_model");
        $rTradeDB	= $trade_model->getTradeData($rWhereData);
        $rTradeData = $rTradeDB[0];

        $rMyDB = $trade_model->userData($rTradeData["user_id"]);
        $rUserDB = $trade_model->userData($rTradeData["offer_id"]);
        $rTradeData["myData"] = $rMyDB[0];
        $rTradeData["traderData"] = $rUserDB[0];

        $this->data = $this->_rCommData;
        $this->data["rScript"] = array_merge($this->data["rScript"], array("local/trade", "rating"));
        $this->data["rDBList"] = $rTradeData;

        $this->views("common/header", $this->data);
        $this->views("trade/confirm", $this->data);
        $this->views("common/footer", $this->data);
    }
}