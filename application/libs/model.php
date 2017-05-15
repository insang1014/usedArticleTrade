<?php
defined('APPPATH') OR exit('No direct script access allowed');

class Model
{
    function __construct()
    {
    }

    protected function queryCombine($rWhereData = array())
    {

        $combineResult = array();

        if( ! is_array($rWhereData) ) {
            return false;
        }

        $i=0;
        foreach ($rWhereData as $key => $val) {

            if( ! is_array($val)) {
                $combineResult["where"][$i] = " AND ".$key." = :".$key;
                $combineResult["execute"][$key] = $val;
            } else {
                foreach ($val as $sKey => $sItem) {
                    switch ($sKey) {
                        case "OR":
                            $combineResult["where"][$i] = " OR ".$key . " = :" . $key;
                            $combineResult["execute"][$key] = $sItem;
                            break;
                        default :
                            $combineResult["where"][$i] = " AND ".$key . " " . $sKey . " :" . $key;
                            $combineResult["execute"][$key] = $sItem;
                            break;
                    }
                }
            }

            $i++;
        }
        return $combineResult;
    }
}