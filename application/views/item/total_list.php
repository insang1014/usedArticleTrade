<!-- Page Content -->
<div class="container">
    <div class="page-header">
        <h1> 거래 물품 리스트 <small>Trade Item List</small></h1>
    </div>

    <div class="searchTable">
    <form id="searchForm" class="form-horizontal">
        <div class="form-group">
            <label class="row col-sm-2 control-label" for="type">거래 구분</label>
            <div class="col-sm-2">
                <select id="type" name="type" class="form-control">
                    <option value="" <?=($rSearchParam["type"] == "") ? "selected" : "";?>>전체</option>
                    <option value="S" <?=($rSearchParam["type"] == "S") ? "selected" : "";?>>판매</option>
                    <option value="P" <?=($rSearchParam["type"] == "P") ? "selected" : "";?>>구매</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="row col-sm-2 control-label" for="searchTitle">제목 검색</label>
            <div class="col-sm-9">
                <input type="text" class="form-control" id="searchTitle" name="searchTitle" placeholder="제목" value="<?=$rSearchParam["searchTitle"]?>" />
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-12 text-center">
                <button class="btn btn-primary" type="submit" id="btn-submit">검색</button>
            </div>
        </div>
    </form>
    </div>

    <br />

    <div class="listTable">
        <table class="table table-bordered table-responsive">
            <colgroup>
                <col width="100">
                <col width="100">
                <col>
                <col width="150">
                <col width="100">
                <col width="150">
            </colgroup>
            <thead>
            <tr class="active">
                <th>번호</th>
                <th>구분</th>
                <th>제목</th>
                <th>작성자</th>
                <th>희망 가격</th>
                <th>등록시간</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if($rDBList !== false) {
                foreach ($rDBList as $rParam) {
            ?>
                    <tr>
                        <td><?=$nNumber?></td>
                        <td><?=($rParam["type"] == "S") ? "판매" : "구매";?></td>
                        <td><a href="/item/itemviewer/<?=$rParam["serial"]?>"><?=$rParam["title"]?></a></td>
                        <td><?= $rParam["user_id"] ?></td>
                        <td><?= number_format($rParam["target_cost"]) ?></td>
                        <td><?= substr($rParam["created"], 2, 8) ?></td>
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
    <!-- Pagination -->
    <?=$sPagination?>
    <!-- /.row -->
</div>