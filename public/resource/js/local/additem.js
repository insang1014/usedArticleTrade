function submitFunc() {
    $("#itemAddForm").submit(function(e){
        e.preventDefault();

        var data, datas, rurl;
        var imageData = new Array;

        datas = new FormData();

        data = $(this).serializeArray();

        for(var i=0; i < data.length; i++) {
            //datas[data[i].name] = data[i].value;
            datas.append(data[i].name, data[i].value);
        }

        for(var i=0; i < $(".upload-hidden").length; i++){
            //imageData[i] = $( ".upload-hidden" )[i].files[0];
            datas.append("imageUpload[]", $( ".upload-hidden" )[i].files[0]);
        }

        if($("#pType").val() == "P") {
            rurl = "/item/purchase";
        } else {
            rurl = "/item/sell";
        }

        $.ajax({
            url: "/item_api/addItem/",
            contentType: 'multipart/form-data',
            type: 'POST',
            data: datas,
            dataType: 'json',
            mimeType: 'multipart/form-data',
            success: function (result) {
                if (result.success) {
                    alert("등록되었습니다.");
                    location.href = rurl;
                } else {
                    alert("등록에 실패하였습니다.");
                    location.href = rurl;
                }
            },
            error : function (jqXHR, textStatus, errorThrown) {
                alert('ERRORS: ' + textStatus);
            },
            cache: false,
            contentType: false,
            processData: false
        });
    });
}

function fileboxControll() {

    $("#addFileDIv").on("click", function () {

        if($("#fileDiv").children(".preview-image").length == 3) {
            alert("최대 3개까지만 등록 가능합니다.");
            return false;
        }

        var fileNumber = (Number($("#fileDiv").children(".preview-image").length)+1);

        var html = "";

        html += '<div class="filebox preview-image">';
        html += '   <input class="upload-name" value="파일선택" disabled="disabled" >';
        html += '   <label class="btn btn-default" for="imageUpload'+fileNumber+'">업로드</label>';
        html += '   <input type="file" id="imageUpload'+fileNumber+'" class="upload-hidden">';
        html += '</div>';

        $("#fileDiv").append(html);
    });

    $("#delFileDIv").on("click", function () {

        if($("#fileDiv").children(".preview-image").length == 0) {
            return false;
        }

        $("#fileDiv").children(".preview-image").last().remove();
    });
}

$(document).ready(function(){
    submitFunc();
    fileboxControll();

    $(document).on("change",".upload-hidden",function(){

        if($(this)[0].files[0] == undefined) {
            var parent = $(this).parent();
            parent.remove();
            return false;
        }

        if(window.FileReader){
            // 파일명 추출
            var filename = $(this)[0].files[0].name;
        }
        else {
            // Old IE 파일명 추출
            var filename = $(this).val().split('/').pop().split('\\').pop();
        };

        $(this).siblings('.upload-name').val(filename);

        /* 썸네일 영역 시작 */
        var parent = $(this).parent();
        parent.children('.upload-display').remove();

        if(window.FileReader){
            //image 파일만
            if (!$(this)[0].files[0].type.match(/image\//)) return;

            var reader = new FileReader();
            reader.onload = function(e){
                var src = e.target.result;
                parent.prepend('<div class="upload-display"><div class="upload-thumb-wrap"><img src="'+src+'" class="upload-thumb"></div></div>');
            }
            reader.readAsDataURL($(this)[0].files[0]);
        } else {
            $(this)[0].select();
            $(this)[0].blur();
            var imgSrc = document.selection.createRange().text;
            parent.prepend('<div class="upload-display"><div class="upload-thumb-wrap"><img class="upload-thumb"></div></div>');

            var img = $(this).siblings('.upload-display').find('img');
            img[0].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(enable='true',sizingMethod='scale',src=\""+imgSrc+"\")";
        }
        /* 썸네일 영역 끝 */

    });
});