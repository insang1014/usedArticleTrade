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
        <h1> 제품 정보 <small>Item Info</small></h1>
    </div>

    <div class="viewer">
        <p><span class="glyphicon glyphicon-triangle-right"></span> 물품 정보</p>
        <table id="viewTable" class="table table-bordered table-responsive">
            <tr>
                <th>제목</th>
                <td colspan="3"><?=$rDBList["title"]?></td>
            </tr>
            <tr>
                <th>내용</th>
                <td colspan="3"><?=$rDBList["contents"]?></td>
            </tr>
            <tr>
                <th>거래 진행상태</th>
                <td><?=$statusTxt?></td>
                <th>희망 가격</th>
                <td><?=number_format($rDBList["target_cost"])?></td>
            </tr>
            <tr>
                <th>첨부 파일</th>
                <td colspan="3">
                    <div class="viewImage">
                        <?php
                        if(is_array($rDBList["imageData"]) && count($rDBList["imageData"]) > 0) {
                            foreach ($rDBList["imageData"] as $rImage) {
                                echo '<img src="/uploads/'.$rImage["file_dir"].'/'.$rImage["save_file_name"].'"> ';
                            }
                        } else {
                            echo "첨부파일 없음";
                        }
                        ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <div class="form-group">
        <div class="text-center">
            <button class="btn btn-primary" type="button" id="btn-submit">거래제안</button>
            <a href="/item/itemList" class="btn btn-danger">목록으로</a>
        </div>
    </div>

    <div class="traderInfo">
        <p><span class="glyphicon glyphicon-triangle-right"></span> 거래자 정보</p>
        <table id="viewTable" class="table table-bordered table-responsive">
            <tr>
                <th>거래자 ID</th>
                <td id="traderId"><?=$rDBList["userData"]["user_id"]?></td>
                <th>거래자명</th>
                <td><?=$rDBList["userData"]["name"]?></td>
            </tr>
            <tr>
                <th>거래자 주소</th>
                <td colspan="3"><?=$rDBList["userData"]["roadaddr"]?></td>
            </tr>
            <tr>
                <th>거래자 평가등급</th>
                <td colspan="3">
                    <div id="rateField"><ul class="c-rating"></ul></div>
                    <div id="ratePoint"></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- 작은 Modal -->
    <div class="modal fade" id="mySmallModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="modalTitle">※ 거래제안하기</h3>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered table-responsive">
                        <tbody id="offerTable">
                        <tr>
                            <th>제안 금액</th>
                            <td>
                                <input type="hidden" id="serial" value="<?=$rDBList["serial"]?>">
                                <input type="text" id="offer_cost" numberonly='true'>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-propose">등록</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">취소</button>
                </div>
            </div>
        </div>
    </div>
</div>