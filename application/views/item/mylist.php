<!-- Page Content -->
<div class="container">
    <div class="page-header">
<?php
    if(!$mode) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".BASEURL);
    }

    if($mode == "P") {
        echo "<h1> 내 구매 품목 <small>Purchasing item</small></h1>";
    } else {
        echo "<h1> 내 판매 품목 <small>Sell item</small></h1>";
    }
?>
    </div>

    <div class="btnTop">
        <form id="searchForm" method="get" class="form-horizontal">
            <div class="form-group">
                <label class="row col-sm-1 control-label" for="type">상태</label>
                <div class="col-sm-2">
                    <select id="status" name="status" class="form-control">
                        <option value="" <?=($rSearchParam["status"] == "") ? "selected" : "";?>>전체</option>
                        <option value="I" <?=($rSearchParam["status"] == "I") ? "selected" : "";?>>진행중</option>
                        <option value="D" <?=($rSearchParam["status"] == "D") ? "selected" : "";?>>대기</option>
                        <option value="C" <?=($rSearchParam["status"] == "C") ? "selected" : "";?>>종료</option>
                    </select>
                </div>
                <div >
                    <a href="/item/addItem/<?=$mode?>"><button type="button" class="btn btn-default pull-right">거래 등록</button></a>
                </div>
            </div>
        </form>
    </div>

    <div class="listTable">
        <table class="table table-bordered table-responsive">
            <colgroup>
                <col width="100">
                <col width="100">
                <col>
                <col width="150">
                <col width="100">
                <col width="80">
                <col width="150">
            </colgroup>
            <thead>
                <tr class="active">
                    <th>번호</th>
                    <th>상태</th>
                    <th>제목</th>
                    <th>희망 가격</th>
                    <th>등록시간</th>
                    <th>제안 건수</th>
                    <th>옵션</th>
                </tr>
            </thead>
            <tbody>
        <?php
            if(count($rDBList) > 0) {
                foreach ($rDBList as $rParam) {
                    $statusColor = "";
                    $statusTxt = "";
                    switch ($rParam["status"]) {
                        case "I":
                            $statusTxt = "진행중";
                            break;
                        case "D":
                            $statusColor = 'class="warning"';
                            $statusTxt = "대기";
                            break;
                        case "C":
                            $statusColor = 'class="danger"';
                            $statusTxt = "종료";
                            break;
                    }
        ?>
                    <tr <?=$statusColor?>>
                        <td><?=$nNumber?></td>
                        <td><?=$statusTxt?></td>
                        <td><?=$rParam["title"]?></td>
                        <td><?= number_format($rParam["target_cost"]) ?></td>
                        <td><?= substr($rParam["created"], 2, 8) ?></td>
                        <td><button type="button" class="btn btn-xs btn-default" onclick="offerModal('<?=$rParam["serial"]?>', '<?=$mode?>', '<?=$rParam["status"]?>')"><?=$rParam["offerCount"]?></button></td>
                        <td>
                        <?php
                            if($rParam["status"] == "I") {
                                echo '<span style="color: #FF0000">수정 불가</span>';
                            } elseif($rParam["status"] == "C") {
                                echo '<span>종료</span>';
                            } else {
                        ?>
                                <a class="btn btn-xs btn-default" href="/item/additem/<?=$mode?>?serial=<?=$rParam["serial"]?>">수정</a>
                                <button class="btn btn-xs btn-default" id="itemDel" onclick="itemDelete('<?=$rParam["serial"]?>')">삭제</button>
                        <?php
                            }
                        ?>
                        </td>
                    </tr>
        <?php
                    $nNumber--;
                }
            } else {
        ?>
                <tr><td colspan="7">등록된 데이터가 없습니다.</td></tr>
        <?php
            }
        ?>
            </tbody>
        </table>

        <!-- 작은 Modal -->
        <div class="modal fade" id="mySmallModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h3 class="modal-title" id="modalTitle">※ 거래요청자 LIST</h3>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-responsive">
                            <thead>
                                <tr class="active">
                                    <th>제안자 ID</th>
                                    <th>제안 금액</th>
                                    <th>요청시간</th>
                                    <th>승인여부</th>
                                </tr>
                            </thead>
                            <tbody id="offerTable">
                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">닫기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <?=$sPagination?>
    <!-- /.row -->

</div>