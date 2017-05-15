<div class="container">
    <div class="page-header">
<?php
    if(!$mode || $rDBList < 1) {
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: ".BASEURL);
    }
?>
    <h1>  수정 <small>modify item</small></h1>
    </div>
    <div class="col-md-7 col-md-offset-2">
        <form id="itemAddForm" class="form-horizontal" method="post" enctype="multipart/form-data">
            <input type="hidden" id="pType" name="type" value="<?=$mode?>" />
            <input type="hidden" name="status" value="D" />
            <div class="form-group">
                <label for="title">제목</label>
                <input type="text" id="title" name="title" value="<?=$rDBList["title"]?>" class="form-control required" data-label="제목을" placeholder="제목">
            </div>
            <div class="form-group">
                <label>내용</label>
                <textarea cols="10" id="contents" name="contents" class="form-control required" data-label="내용을" placeholder="내용을 입력하세요"><?=$rDBList["contents"]?></textarea>
            </div>
            <div class="form-group">
                <label for="user_pwd">희망 가격</label>
                <input id="target_cost" name="target_cost" type="text" value="<?=$rDBList["target_cost"]?>" class="form-control required" data-label="희망가격을" placeholder="희망 가격" maxlength="10" numberonly="true">
            </div>
            <div class="form-group">
                <label>첨부파일</label>
                <div id="fileDiv">
                    <div class="filebox">
                        <button type="button" class="btn btn-default" id="addFileDIv">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn btn-default" id="delFileDIv">
                            <span class="glyphicon glyphicon-minus" aria-hidden="true"></span>
                        </button>
                    </div>
                <?php
                    foreach ($rDBList["imageData"] as $value) {
                ?>
                        <div class="filebox preview-image">기존파일 :
                            <a href="#" onclick="window.open('<?=BASEURL."uploads/".$value["file_dir"]."/".$value["save_file_name"]?>')"><?=$value["origin_file_name"]?></a>
                        </div>
                <?php
                    }
                ?>
                </div>
            </div>

            <div class="form-group">
                <div class="text-center">
                    <button class="btn btn-primary" type="submit" id="btn-modify">수정</button>
                    <button class="btn btn-danger" type="button">취소</button>
                </div>
            </div>
        </form>
    </div>
</div>