<div class="container">
    <div class="page-header">
<?php
    if(!$mode) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".BASEURL);
    }

    if($mode == "P") {
        echo "<h1> 구매 등록 <small>Add Purchasing item</small></h1>";
    } else {
        echo "<h1> 판매 등록 <small>Add Selling Item</small></h1>";
    }
?>
    </div>
    <div class="col-md-7 col-md-offset-2">
        <form id="itemAddForm" class="form-horizontal" method="post" enctype="multipart/form-data">
            <input type="hidden" id="pType" name="type" value="<?=$mode?>" />
            <input type="hidden" name="status" value="D" />
            <div class="form-group">
                <label for="title">제목</label>
                    <input type="text" id="title" name="title" class="form-control required" data-label="제목을" placeholder="제목">
            </div>
            <div class="form-group">
                <label>내용</label>
                <textarea cols="10" id="contents" name="contents" class="form-control required" data-label="내용을" placeholder="내용을 입력하세요"></textarea>
            </div>
            <div class="form-group">
                <label for="user_pwd">희망 가격</label>
                    <input id="target_cost" name="target_cost" type="text" class="form-control required" data-label="희망가격을" placeholder="희망 가격" maxlength="10" numberonly="true">
            </div>
<!--            <div class="form-group">-->
<!--                <label>진행상태</label>-->
<!--                <div>-->
<!--                    <label class="radio-inline"><input type="radio" name="status" id="status_d" value="D" checked readonly> 대기 </label>-->
<!--                    <label class="radio-inline"><input type="radio" name="status" id="status_i" value="I"> 진행 </label>-->
<!--                </div>-->
<!--            </div>-->
            <div class="form-group">
                <label>파일첨부</label>
                <div id="fileDiv">
                    <div class="filebox">
                        <button type="button" class="btn btn-default" id="addFileDIv">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn btn-default" id="delFileDIv">
                            <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="text-center">
                    <button class="btn btn-primary" type="submit" id="btn-submit">등록</button>
                    <button class="btn btn-danger" type="button">취소</button>
                </div>
            </div>
        </form>
    </div>
</div>