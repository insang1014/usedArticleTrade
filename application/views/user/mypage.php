<div class="container">
    <div class="page-header">
        <h1> My Page</h1>
    </div>

    <div class="viewer">
        <p>
            <span class="glyphicon glyphicon-triangle-right"></span> 내 정보
            <button class="btn btn-sm btn-primary mileage-btn" type="button" id="btn-submit">마일리지 충전</button>
        </p>
        <table id="viewTable" class="table table-bordered table-responsive">
            <tr>
                <th>ID</th>
                <td id="myId"><?=$rDBList["user_id"]?></td>
                <th>성명</th>
                <td><?=$rDBList["name"]?></td>
            </tr>
            <tr>
                <th>주소</th>
                <td colspan="3"><?=$rDBList["roadaddr"]?></td>
            </tr>
            <tr>
                <th>내 마일리지</th>
                <td colspan="3"><?=number_format($rDBList["mileage"])?></td>
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

    <div class="tradeList">
        <p><span class="glyphicon glyphicon-triangle-right"></span> 승인대기 중 거래내역 </p>
        <table class="table table-bordered table-responsive">
            <thead>
            <tr class="active">
                <th>거래 구분</th>
                <th>내 거래 형태</th>
                <th>등록자</th>
                <th>요청자</th>
                <th>거래 가격</th>
                <th>등록시간</th>
                <th>상태</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($rDBList["tradeList"]) > 0) {
                foreach ($rDBList["tradeList"] as $rParam) {
                    ?>
                    <tr>
                        <td><?=$rParam["typeText"]?></td>
                        <td><?=$rParam["gubun"]?></td>
                        <td><?= $rParam["user_id"] ?></td>
                        <td><?= $rParam["offer_id"] ?></td>
                        <td><?= number_format($rParam["cost"]) ?></td>
                        <td><?= substr($rParam["created"], 2, 8) ?></td>
                        <td>
                        <?php
                            if($_SESSION["user"]["UserID"] == $rParam["offer_id"]) {
                        ?>
                            <button class="btn btn-sm btn-success" type="button" onclick="finalApprove('<?=$rParam["type"]?>','<?=$rParam["user_id"]?>','<?=$rParam["item_serial"]?>')">최종승인</button>
                        <?php
                            } else {
                        ?>
                            <span style="color: #FF2222">승인 대기중</span>
                        <?php
                            }
                        ?>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr><td colspan="7">진행중인 거래내역이 없습니다.</td></tr>
                <?php
            }
            ?>
            </tbody>
        </table>
        <!-- Pagination -->
        <?=$sPagination?>
        <!-- /.row -->
    </div>

    <hr />

    <div class="ratingList">
        <p><span class="glyphicon glyphicon-triangle-right"></span> 평가 대기목록 </p>
        <table class="table table-bordered table-responsive">
            <thead>
            <tr class="active">
                <th>거래 구분</th>
                <th>내 거래 형태</th>
                <th>등록자</th>
                <th>요청자</th>
                <th>거래 가격</th>
                <th>등록시간</th>
                <th>상태</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(count($rDBList["ratingList"]) > 0) {
                foreach ($rDBList["ratingList"] as $rParam) {
                    ?>
                    <tr>
                        <td><?=$rParam["typeText"]?></td>
                        <td><?=$rParam["gubun"]?></td>
                        <td><?= $rParam["user_id"] ?></td>
                        <td><?= $rParam["offer_id"] ?></td>
                        <td><?= number_format($rParam["cost"]) ?></td>
                        <td><?= substr($rParam["created"], 2, 8) ?></td>
                        <td>
                            <input type="hidden" id="this_serial" value="<?= $rParam["item_serial"] ?>">
                            <input type="hidden" id="target_id" value="<?= $rParam["rate_id"] ?>">
                            <button class="btn btn-sm btn-primary btn-rating" type="button">평가하기</button>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr><td colspan="7">평가 대기목록이 없습니다.</td></tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

    <!-- 마일리지 Modal -->
    <div class="modal fade" id="mileageModal" tabindex="-1" role="dialog" aria-labelledby="mileageModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="modalTitle">※ 마일리지 충전</h3>
                </div>
                <div class="modal-body">
                    <form id="mileageForm" name="mileageForm" method="post">
                    <table class="table table-bordered table-responsive modal-table">
                        <tbody>
                        <tr>
                            <th>충전금액</th>
                            <td>
                                <select id="charge_cost" name="charge_cost" class="form-control">
                                    <!-- <option value="A">관리자 승인</option> -->
                                    <option value="1000">1,000</option>
                                    <option value="5000">5,000</option>
                                    <option value="10000">10,000</option>
                                    <option value="20000">20,000</option>
                                    <option value="50000">50,000</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>충전방식</th>
                            <td>
                                <select id="charge_type" name="charge_type" class="form-control">
                                    <!-- <option value="A">관리자 승인</option> -->
                                    <option value="B">구매</option>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-charge">충전</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">닫기</button>
                </div>
            </div>
        </div>
    </div>

    <!-- 평가 Modal -->
    <div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="ratingModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h3 class="modal-title" id="modalTitle">※ 고객 평가 </h3>
                </div>
                <div class="modal-body">
                    <form id="rateForm" name="rateForm" method="post">
                        <input type="hidden" id="item_serial" name="item_serial" />
                        <input type="hidden" id="rate_id" name="rate_id" />
                    <table class="table table-bordered table-responsive modal-table">
                        <tbody>
                        <tr>
                            <th>고객 점수</th>
                            <td>
                                <label class="radio-inline"><input type="radio" name="rate" id="rate1" value="1"> 1점 </label>
                                <label class="radio-inline"><input type="radio" name="rate" id="rate2" value="2"> 2점 </label>
                                <label class="radio-inline"><input type="radio" name="rate" id="rate3" value="3"> 3점 </label>
                                <label class="radio-inline"><input type="radio" name="rate" id="rate4" value="4"> 4점 </label>
                                <label class="radio-inline"><input type="radio" name="rate" id="rate5" value="5" checked> 5점 </label>
                            </td>
                        </tr>
                        <tr>
                            <th>거래 평가</th>
                            <td>
                                <textarea rows="5" class="form-control" id="memo" name="memo"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="btn-rateSubmit">평가</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">닫기</button>
                </div>
            </div>
        </div>
    </div>

</div>