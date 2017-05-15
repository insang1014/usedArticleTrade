function offerModal(serial, mode, status) {

    var html = "";
    $("#offerTable").html("");

    $.post("/item_api/getOfferData/", {"item_serial":serial}, function (result) {
        if (result.success) {

            for(var i=0; i < result.data.length; i++) {

                if(status == "C")
                {
                    if(result.data[i].approve_yn != "Y") {
                        continue;
                    }
                }

                html += '<tr>';
                html += '   <td>'+result.data[i].offer_id+'</td>';
                html += '   <td>'+numberWithCommas(result.data[i].offer_cost)+'</td>';
                html += '   <td>'+result.data[i].created+'</td>';
                html += '   <td>';
                if(result.data[i].approve_yn == "Y") {
                    if(status == "C") {
                        html += '       <span>거래 완료</span>';
                    } else {
                        html += '       <span>거래 진행중</span>';
                    }
                } else {
                    html += '       <button type="button" class="btn btn-xs btn-default" id="approveBtn" onclick="fncApproveOffer(\''+serial+'\',\''+result.data[i].serial+'\',\''+mode+'\')">승인</button>';
                    html += '       <input type="hidden" id="offer_serial" value=' + result.data[i].serial + ' />';
                }
                html += '   </td>';
                html += '</tr>';
            }

            $("#offerTable").append(html);
        } else {
            html += '<tr>';
            html += '   <td colspan="4">등록된 거래 요청이 없습니다.</td>';
            html += '</tr>';
            $("#offerTable").append(html);
        }
    });

    $('#mySmallModal').modal("show");
}

function fncApproveOffer(item_serial, offer_serial, mode) {
    var isTrue = confirm("해당요청을 승인하시겠습니까?");

    if(isTrue) {
        location.href = "/trade/?item_serial="+item_serial+"&offer_serial="+offer_serial+"&mode="+mode;
    }
}

function itemDelete(serial) {

    var isTrue = confirm("해당내역을 삭제하시겠습니까?");

    if(isTrue) {
        $.post("/item_api/itemDelete/", {"serial": serial}, function (result) {
            if (result.success) {
                alert("삭제되었습니다.");
                location.reload();
            } else {
                alert(result.data.message);
                location.reload();
            }
        });
    }
}

$(document).ready(function(){

    $("#status").on("change", function () {
        $("#searchForm").submit();
    });
});