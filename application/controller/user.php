<?php
defined('APPPATH') OR exit('No direct script access allowed');

class User extends Controller
{
    private $_nLimitRowcnt	= 3;		// 페이지당 컨텐츠 갯수
    private $_rPaging		= array();	// 공통 페이징 데이터
    private $_rCommData = array();	// 공통 데이터
    private $data;


    public function __construct()
    {
        parent::__construct();

        // 공통 데이터
        $this->_rCommData = array(
            "rCss" => array("local/user"),
            "rScript" => array("local/common")
        );
    }

    public function index()
    {
        $this->login();
    }

    public function login()
    {

        $this->data = $this->_rCommData;
        $this->data["rScript"] = array_merge($this->data["rScript"], array("local/user_login"));

        $this->views("common/header", $this->data);
        $this->views("user/login", $this->data);
        $this->views("common/footer", $this->data);
    }

    public function register()
    {

        $this->data = $this->_rCommData;

        $this->data["rCss"] = array_merge($this->data["rCss"], array("bootstrap-datepicker"));
        $this->data["rScript"] = array_merge($this->data["rScript"], array("bootstrap-datepicker", "local/user_register"));

        $this->views("common/header", $this->data);
        $this->views("user/register", $this->data);
        $this->views("common/footer", $this->data);
    }

    public function logout()
    {
        unset($_SESSION["user"]);
        header("Location: /");
    }

    public function mypage()
    {

        $nOffset = 0;
        $nowPage = 1;
        if(isset($_GET["page"])) {
            if($_GET["page"] > 0)
            {
                $nowPage = $_GET["page"];
                $nOffset = ($nowPage -1) * $this->_nLimitRowcnt;
            }
        }

        $rWhereData	= array();
        $rWhereData["user_id"] = $_SESSION["user"]["UserID"];

        // DB
        $user_model = $this->loadModel("user_model");
        $rUserDB	= $user_model->getDBData(0, 0, "users", $rWhereData);
        $rUserData = $rUserDB[0];

        // TradeDB
        $rWhereData["offer_id"]["OR"] = $_SESSION["user"]["UserID"];

        $rCntWhere = $rWhereData;
        $rCntWhere["status"] = "Y";
        $nTotalCnt	= $user_model->getDBData(0, 0, "approve_trade", $rCntWhere, true);
        $rTradeData	= $user_model->getDBData($nOffset, $this->_nLimitRowcnt, "approve_trade", $rWhereData);

        $rTradeDT = array();
        $rTradeType = array("S" => "판매", "P"=>"구매");

        foreach ($rTradeData as $key => $item) {

            $item["typeText"] = $rTradeType[$item["type"]];

            if($_SESSION["user"]["UserID"] == $item["user_id"]) {
                $item["gubun"] = $rTradeType[$item["type"]]."등록 물품";
                $item["rate_id"] = $item["offer_id"];
            } elseif ($_SESSION["user"]["UserID"] == $item["offer_id"]) {
                $item["gubun"] = $rTradeType[$item["type"]]."요청 물품";
                $item["rate_id"] = $item["user_id"];
            }

            if($item["status"] == "Y") {
                $rTradeDT["tradeList"][] = $item;
            } else {
                if(!$user_model->getDBData(0, 0, "rating", array("item_serial" => $item["item_serial"], "user_id" => $_SESSION["user"]["UserID"]), true)){
                    $rTradeDT["ratingList"][] = $item;
                }
            }
        }

        $rUserData["tradeList"] = (isset($rTradeDT["tradeList"])) ? $rTradeDT["tradeList"] : array();
        $rUserData["ratingList"] = (isset($rTradeDT["ratingList"])) ? $rTradeDT["ratingList"] : array();

        // Paging
        $rPaging = $this->_rPaging;
        $rPaging['base_url'] = BASEURL.$_GET["url"];
        $rPaging["total_col"] = $nTotalCnt;
        $rPaging["now_page"] = $nowPage;
        $rPaging["page_col_num"] = $this->_nLimitRowcnt;
        $rPaging["page_block_num"] = 5;

        $this->data = $this->_rCommData;

        $this->data["rCss"] = array_merge($this->data["rCss"], array("bootstrap-datepicker"));
        $this->data["rScript"] = array_merge($this->data["rScript"], array("local/mypage", "rating"));
        $this->data["rDBList"] = $rUserData;
        $this->data['sPagination'] = paging($rPaging);

        $this->views("common/header", $this->data);
        $this->views("user/mypage", $this->data);
        $this->views("common/footer", $this->data);
    }
}