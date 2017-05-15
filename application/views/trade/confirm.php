<?php
if(!$rDBList) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: ".BASEURL);
}

$statusTxt = "";
switch ($rDBList["status"]) {
    case "I":
        $statusTxt = "진행중";
        break;
    case "D":
        $statusTxt = "대기";
        break;
    case "C":
        $statusTxt = "종료";
        echo "종료";
        break;
}

?>
<div class="container">
    <div class="page-header">
        <h1> 거래 승인 <small>Trade Approve</small></h1>
    </div>

    <div class="viewer">
        <p><span class="glyphicon glyphicon-triangle-right"></span> 물품 정보</p>
        <table id="viewTable" class="table table-bordered table-responsive">
            <tr>
                <th>제목</th>
                <td><?=$rDBList["title"]?></td>
                <th>거래 진행상태</th>
                <td><?=$statusTxt?></td>
            </tr>
            <tr>
                <th>내용</th>
                <td colspan="3"><?=$rDBList["contents"]?></td>
            </tr>
          </table>
    </div>
    <div id="tradeInfo">
        <p><span class="glyphicon glyphicon-triangle-right"></span> 결제정보</p>
        <div id="tradeField">
            <table id="price_info">
                <tr>
                    <th>희망 가격</th>
                    <td><?=number_format($rDBList["target_cost"])?></td>
                    <th>제안 가격</th>
                    <td><?=number_format($rDBList["offer_cost"])?></td>
                    <th>거래 후 마일리지</th>
                    <td><?=number_format($rDBList["myData"]["mileage"]+$rDBList["offer_cost"])?></td>
                </tr>
            </table>
        </div>
    </div>
    <div id="userInfo">
        <div class="myInfo">
            <p><span class="glyphicon glyphicon-triangle-right"></span> 내 정보</p>
            <table id="viewTable" class="table table-bordered table-responsive">
                <tr>
                    <th>ID</th>
                    <td><?=$rDBList["myData"]["user_id"]?></td>
                    <th>성명</th>
                    <td><?=$rDBList["myData"]["name"]?></td>
                </tr>
                <tr>
                    <th>주소</th>
                    <td colspan="3"><?=$rDBList["myData"]["roadaddr"]?></td>
                </tr>
                <tr>
                    <th>내 마일리지</th>
                    <td colspan="3"><?=number_format($rDBList["myData"]["mileage"])?></td>
                </tr>
            </table>
        </div>
        <div class="traderInfo">
            <p><span class="glyphicon glyphicon-triangle-right"></span> 거래자 정보</p>
            <table id="viewTable" class="table table-bordered table-responsive">
                <tr>
                    <th>ID</th>
                    <td id="traderId"><?=$rDBList["traderData"]["user_id"]?></td>
                    <th>성명</th>
                    <td><?=$rDBList["traderData"]["name"]?></td>
                </tr>
                <tr>
                    <th>주소</th>
                    <td colspan="3"><?=$rDBList["traderData"]["roadaddr"]?></td>
                </tr>
                <tr>
                    <th>평가등급</th>
                    <td colspan="3">
                        <div id="rateField"><ul class="c-rating"></ul></div>
                        <div id="ratePoint"></div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div id="formField">
        <form id="tradeForm" method="post">
            <input type="hidden" name="itemSerial" value="<?=$rDBList["serial"]?>">
            <input type="hidden" name="offerSerial" value="<?=$rDBList["t_o_serial"]?>">
            <input type="hidden" name="user_id" value="<?=$rDBList["myData"]["user_id"]?>">
            <input type="hidden" name="offer_id" value="<?=$rDBList["traderData"]["user_id"]?>">
            <input type="hidden" name="cost" value="<?=$rDBList["offer_cost"]?>">
            <input type="hidden" name="type" value="<?=$rDBList["type"]?>">
        </form>
    </div>
    <div>
        <div class="text-center">
            <button class="btn btn-primary" type="button" id="btn-submit">거래 승인</button>
            <button class="btn btn-danger" type="button" id="btn-cancel">거래 취소</button>
        </div>
    </div>
</div>