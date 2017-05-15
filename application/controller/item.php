<?php
defined('APPPATH') OR exit('No direct script access allowed');

class Item extends Controller
{
    private $_nLimitRowcnt	= 10;		// 페이지당 컨텐츠 갯수
    private $_rPaging		= array();	// 공통 페이징 데이터
    private $_rCommData = array();	// 공통 데이터
    private $data;

    public function __construct()
    {
        parent::__construct();

        // 세선 로그인 검증
        $this->session_validation();

        // 공통 데이터
        $this->_rCommData = array(
            "rCss" => array("local/item"),
            "rScript" => array("local/common")
        );
    }

    public function index()
    {
        $this->itemList();
    }

    public function sell()
    {
        $this->myList("S");
    }

    public function purchase()
    {
        $this->myList("P");
    }

    // 판매 품목 리스트 페이지
    public function myList($mode)
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

        // 검색 parameter
        $rSearchParam = array();
        $rWhereData	= array();
        foreach (array("status") as $sSearchParam) {
            $rSearchParam[$sSearchParam] = "";

            if(isset($_GET[$sSearchParam])) {
                if ($_GET[$sSearchParam]) {
                    $rSearchParam[$sSearchParam] = $_GET[$sSearchParam];
                    $rWhereData[$sSearchParam] = $_GET[$sSearchParam];
                }
            }
        }

        $rWhereData["user_id"] = $_SESSION["user"]["UserID"];
        $rWhereData["type"] = $mode;

        $item_model = $this->loadModel('item_model');
        $nTotalCnt	= $item_model->getDBData(0, 0, "trade_item", $rWhereData, true);
        $rItemData	= $item_model->getDBData($nOffset, $this->_nLimitRowcnt, "trade_item", $rWhereData);

        foreach ($rItemData as $key => $item)
        {
            $rItemData[$key]["offerCount"] = $item_model->getDBData(0, 0, "trade_offer", array("item_serial"=>$item["serial"]), true);
        }

        // Paging
        $rPaging = $this->_rPaging;
        $rPaging['base_url'] = BASEURL.$_GET["url"];
        $rPaging["total_col"] = $nTotalCnt;
        $rPaging["now_page"] = $nowPage;
        $rPaging["page_col_num"] = $this->_nLimitRowcnt;
        $rPaging["page_block_num"] = 5;
        $rPaging["suffix"] = $rSearchParam;

        $this->data = $this->_rCommData;
        $this->data["rScript"] = array_merge($this->data["rScript"], array("local/mylist"));
        $this->data["rDBList"] = $rItemData;
        $this->data["nNumber"] = $nTotalCnt - (($nowPage-1) * $this->_nLimitRowcnt);
        $this->data["mode"] = $mode;
        $this->data['sPagination'] = paging($rPaging);
        $this->data["rSearchParam"] = $rSearchParam;

        $this->views("common/header", $this->data);
        $this->views("item/mylist", $this->data);
        $this->views("common/footer", $this->data);
    }

    public function additem($mode)
    {

        $this->data = $this->_rCommData;
        $this->data["rScript"] = array_merge($this->data["rScript"], array("local/additem"));
        $this->data["mode"] = $mode;

        $this->views("common/header", $this->data);

        $rItemData = array();
        if(isset($_GET["serial"])) {
            $rWhereData	= array();
            $rWhereData["serial"] = $_GET["serial"];

            // DB
            $item_model = $this->loadModel('item_model');
            $rItemDB	= $item_model->getDBData(0, 0, "trade_item", $rWhereData);
            $rItemData = $rItemDB[0];
            $rItemData["imageData"] = $item_model->getDBData(0, 0, "trade_item_image", array("item_serial" => $rItemData["serial"]));

            $this->data["rDBList"] = $rItemData;
            $this->views("item/modifyItem", $this->data);
        } else {
            $this->views("item/additem", $this->data);
        }
        $this->views("common/footer", $this->data);
    }

    public function itemviewer($serial)
    {

        $rWhereData	= array();
        $rWhereData["serial"] = $serial;

        // DB
        $item_model = $this->loadModel('item_model');
        $rItemDB	= $item_model->getDBData(0, 0, "trade_item", $rWhereData);
        $rItemData = $rItemDB[0];

        if($rItemData) {
            // UserDB
            $rUnsetParam = array("serial","user_pwd", "mileage", "created");
            $rUserDB = $item_model->getDBData(0, 1, "users", array("user_id"=>$rItemData["user_id"]), false, $rUnsetParam);
            unset($rUserDB[0]["user_pwd"]);
            $rItemData["userData"] = $rUserDB[0];

            $rItemData["imageData"] = $item_model->getDBData(0, 0, "trade_item_image", array("item_serial" => $rItemData["serial"]));
        }

        $this->data = $this->_rCommData;
        $this->data["rScript"] = array_merge($this->data["rScript"], array("local/item_view", "rating"));
        $this->data["rDBList"] = $rItemData;


        $this->views("common/header", $this->data);
        $this->views("item/item_view", $this->data);
        $this->views("common/footer", $this->data);
    }

    public function itemList()
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

        // 검색 parameter
        $rSearchParam = array();
        $rWhereData	= array();
        foreach (array("type", "searchTitle") as $sSearchParam) {
            $rSearchParam[$sSearchParam] = "";

            if(isset($_GET[$sSearchParam])) {
                if ($_GET[$sSearchParam]) {
                    $rSearchParam[$sSearchParam] = $_GET[$sSearchParam];

                    if($sSearchParam == "searchTitle") {
                        $rWhereData["title"]["like"] = "%".$_GET[$sSearchParam]."%";
                    } else {
                        $rWhereData[$sSearchParam] = $_GET[$sSearchParam];
                    }
                }
            }
        }

        $rWhereData["user_id"]["!="] = $_SESSION["user"]["UserID"];
        $rWhereData["status"] = "D";

        // DB
        $item_model = $this->loadModel('item_model');
        $nTotalCnt	= $item_model->getDBData(0, 0, "trade_item", $rWhereData, true);
        $rItemData	= $item_model->getDBData($nOffset, $this->_nLimitRowcnt, "trade_item", $rWhereData);

        // Paging
        $rPaging = $this->_rPaging;
        $rPaging['base_url'] = BASEURL.$_GET["url"];
        $rPaging["total_col"] = $nTotalCnt;
        $rPaging["now_page"] = $nowPage;
        $rPaging["page_col_num"] = $this->_nLimitRowcnt;
        $rPaging["page_block_num"] = 5;
        $rPaging["suffix"] = $rSearchParam;

        $this->data = $this->_rCommData;
        $this->data["rScript"] = array_merge($this->data["rScript"], array("local/item_list"));
        $this->data["rDBList"] = $rItemData;
        $this->data["nNumber"] = $nTotalCnt - (($nowPage-1) * $this->_nLimitRowcnt);
        $this->data['sPagination'] = paging($rPaging);
        $this->data["rSearchParam"] = $rSearchParam;

        $this->views("common/header", $this->data);
        $this->views("item/total_list", $this->data);
        $this->views("common/footer", $this->data);
    }
}