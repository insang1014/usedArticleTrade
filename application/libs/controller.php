<?php
defined('APPPATH') OR exit('No direct script access allowed');

class Controller
{
    public $db = null;

    function __construct()
    {
        $this->dbConnect();
    }

    private function dbConnect()
    {
        try
        {
            $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
            $this->db = new PDO(DB_PDODRIVER . ":host=" . DB_HOST . ";dbname=" . DB_DATABASE, DB_USER, DB_PASSWORD, $options);
        }
        catch (\PDOException $e)
        {
            throw new $e->getMessage();
            exit;
        }
    }

    public function session_validation()
    {
        if(!isset($_SESSION['user'])) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.LOGINURL);
        }
    }

    public function loadModel($model_name)
    {
        require_once APPPATH.'libs/model.php';
        require APPPATH.'models/' . strtolower($model_name) . '.php';
        return new $model_name($this->db);
    }

    protected function response($success, $data = array())
    {
        header('Content-Type: application/json');

        $this->data['success'] = $success;
        $this->data['data'] = $data;

        echo json_encode($this->data);
    }

    protected function views($sPath, $rParam = array())
    {
        $file_exists = FALSE;

        $_viewPath = "";
        $_ext = pathinfo($sPath, PATHINFO_EXTENSION);
        $_file = ($_ext === '') ? $sPath.'.php' : $sPath;

        if (file_exists(VIEWPATH.$_file)) {
            $_viewPath = VIEWPATH.$_file;
            $file_exists = TRUE;
        }

        if ( ! $file_exists && ! file_exists($_viewPath)) {
            exit('404 NOT FOUND : '.$_file);
        }

        if (is_array($rParam) && count($rParam) > 0) {
            extract($rParam);
        }

        include($_viewPath);
    }
}