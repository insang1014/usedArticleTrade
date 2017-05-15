<?php
defined('APPPATH') OR exit('No direct script access allowed');

class index extends Controller
{
    private $_rCommData = array();	// 공통 데이터
    private $data;

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->intro();
    }

    public function intro()
    {
        $this->views("common/header", $this->data);
        $this->views("index", $this->data);
        $this->views("common/footer", $this->data);
    }
}