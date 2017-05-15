<?php

if ( ! function_exists('cssForLayout'))
{
    function cssForLayout($rCssFile)
    {
        $inHtml	 = "";
        $sCssUrl = "resource/css/";

        foreach($rCssFile as $sEachCss) {
            if (!file_exists($sCssUrl.$sEachCss.'.css')) {
                continue;
            }

            $inHtml .= '<link rel="stylesheet" type="text/css" href="/'.$sCssUrl.$sEachCss.'.css" />'."\n";
        }

        return $inHtml;
    }
}

if ( ! function_exists('scriptForLayout'))
{
    function scriptForLayout($rScriptFile)
    {
        $inHtml	 = "";
        $sScriptUrl = "resource/js/";

        foreach($rScriptFile as $sEachScript) {
            if (!file_exists($sScriptUrl.$sEachScript.'.js')) {
                continue;
            }
            $inHtml .= '<script type="text/javascript" src="/'.$sScriptUrl.$sEachScript.'.js"></script>'."\n";
        }

        return $inHtml;
    }
}


/**
 * @desc : Curl 전송 함수
 **/
if ( ! function_exists('getUrl_curl'))
{
    function getUrl_curl($url, $method = 'GET')
    {
        $ch = curl_init();
        $agent = 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.0; Trident/5.0)';

        switch(strtoupper($method)) {
            case 'GET':
                curl_setopt($ch, CURLOPT_URL, $url);
                break;

            case 'POST':
                $info = parse_url($url);
                $url = $info['scheme'] . '://' . $info['host'] . isset($info['path']);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                if(isset($info['query']))
                {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $info['query']);
                }
                break;

            default:
                return false;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_USERAGENT, $agent);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
}


/**
 * @desc : Curl 전송 함수
 * $total_col // 총 게시글 수
 * $now_page // 현재 페이지 번호
 * $page_col_num // 한 페이지 게시글 수
 * $page_block_num // 한 페이지 블럭 수
 **/
if ( ! function_exists('paging')) {
    function paging($rPaging = array())
    {
        $pagination_html = "";
        $total = $rPaging["total_col"]; // 총 컬럼 수
        $page = $rPaging["now_page"]; // 현재 페이지
        $page_num = $rPaging["page_col_num"]; // 한 페이지 컬럼 수
        $block_num = $rPaging["page_block_num"]; // 한 페이지 블럭 수

        $sQueryString = "";
        if(isset($rPaging["suffix"])) {
            if(is_array($rPaging["suffix"])) {
                foreach ($rPaging["suffix"] as $key => $value) {
                    $sQueryString .= "&".$key."=".$value;
                }
            }
        }

        $limit_start = $page_num * $page - $page_num; // limit 시작 위치

        $total_page = ceil($total / $page_num); // 총 페이지
        $total_black = ceil($total_page / $block_num); // 총 블럭

        $now_block = ceil($page / $block_num); // 현재 페이지의 블럭
        $start_page = (($now_block * $block_num) - ($block_num - 1)); // 가져올 페이지의 시작번호
        $last_page = ($now_block * $block_num); // 가져올 마지막 페이지 번호

        $prev_page = ($now_block * $block_num) - $block_num; // 이전 블럭 이동시 첫 페이지
        $next_page = ($now_block * $block_num) + 1; // 다음 블럭 이동시 첫 페이지

        $pagination_html .= '<div class="row text-center">';
        $pagination_html .= '    <div class="col-lg-12">';
        $pagination_html .= '        <ul class="pagination">';

        // 이전 페이지
        if ($now_block > 1) {
            $pagination_html .= '        <li><a href="'.$rPaging["base_url"].'?page='.$prev_page.$sQueryString.'">&laquo;</a></li>';
        }
        // echo "이전 페이지 : $prev_page";
        // 페이지 리스트
        if ($last_page < $total_page) {
            $for_end = $last_page;
        } else {
            $for_end = $total_page;
        }
        for ($i = $start_page; $i <= $for_end; $i++) {
            $pagination_html .= '        <li><a href="'.$rPaging["base_url"].'?page='.$i.$sQueryString.'">'.$i.'</a></li>';
        }
        // 다음 페이지
        if ($now_block < $total_black) {
            $pagination_html .= '        <li><a href="'.$rPaging["base_url"].'?page='.$next_page.$sQueryString.'">&raquo;</a></li>';
        }
        // echo "다음 페이지 : $next_page";

        $pagination_html .= '        </ul>';
        $pagination_html .= '    </div>';
        $pagination_html .= '</div>';

        return $pagination_html;
    }
}