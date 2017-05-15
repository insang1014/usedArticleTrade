<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" type="text/css" href="/resource/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="/resource/css/local/common.css" />
<?php
if (isset($rCss)) {
    echo cssForLayout($rCss);
    unset($rCss);
}
$nowUrl = "";
if(isset($_GET['url'])) {
    $nowUrl = $_GET['url'];
}
$matchUrl = rtrim($nowUrl, '/');
$matchUrl = filter_var($matchUrl, FILTER_SANITIZE_URL);

// 탑메뉴 목록
$topMenuList = array();

if(isset($_SESSION["user"])) {
    array_push($topMenuList, array("name"=>"거래 물품 리스트", "url"=>"item/itemList"));
    array_push($topMenuList, array("name"=>"내 판매 품목", "url"=>"item/sell"));
    array_push($topMenuList, array("name"=>"내 구매 품목", "url"=>"item/purchase"));
}

?>
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-inverse" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <div class="navbar-brand"><b>Used Item Trade</b></div>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
        <?php
            foreach ($topMenuList as $item) {
                $addClass = "";
                if(stripos($matchUrl, $item["url"]) !== false) {
                    $addClass = 'class="active"';
                }
        ?>
                <li <?=$addClass?>>
                    <a href="<?=DIRECTORY_SEPARATOR.$item["url"]?>"><?=$item["name"]?></a>
                </li>
        <?php
            }
        ?>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php
                if( isset($_SESSION["user"]) ) {
                    ?>
                    <li><a href="/user/mypage"><span class="glyphicon glyphicon-user"></span> 마이페이지</a></li>
                    <li><a href="/user/logout"><span class="glyphicon glyphicon-log-in"></span> 로그아웃</a></li>
                    <?php
                } else {
                    ?>
                    <li><a href="/user/register"><span class="glyphicon glyphicon-user"></span> 회원가입</a></li>
                    <li><a href="/user/login"><span class="glyphicon glyphicon-log-in"></span> 로그인</a></li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container -->
</nav>

<div class="wrapper">