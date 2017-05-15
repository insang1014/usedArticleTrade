// ADD RATING WIDGET
function addRatingWidget() {
    var myId = $("#myId").text();

    var ratingElement = document.querySelector(".c-rating")
    var currentRating = 0;
    var maxRating = 5;
    //var callback = function() {}

    $.ajax({
        type: "post",
        url: "/item_api/getRatingAvg",
        data: {rate_id:myId},
        dataType:"json",
        success: function(result) {
            if (result.success) {
                currentRating = result.data.rateAvg;
                var r = rating(ratingElement, currentRating, maxRating, '');
                $(".c-rating").attr("title", currentRating);
                $("#ratePoint").text('( 평점 : '+ currentRating +'점 )');
            } else {
                alert("Rated Failed !!");
            }
        },
        error: function(data) {
            console.log(data);
        }
    });
}

function finalApprove(type, user_id, item_serial) {

    var isTrue = false;
    isTrue = confirm("최종 승인처리 하시겠습니까??");

    if(isTrue) {
        $.post("/trade_api/closeItemTrade", {"type":type, "user_id":user_id,"item_serial":item_serial}, function (result) {
            if (result.success) {
                alert("최종 거래 승인처리 되었습니다.\n\n거래평가를 해주세요.");
                location.reload();
            } else {
                alert(result.data.message);
                location.reload();
            }
        });
    }
}

function mileageCharge() {

    $("#btn-submit").on("click", function () {
        $('#mileageModal').modal("show");
    });

    $("#btn-charge").on("click", function () {

        var isTrue = false;
        isTrue = confirm("마일리지를 충전하시겠습니까??");

        if(isTrue) {
            //$("#mileageForm").attr('action', '/user_api/mileageCharge').submit();
            $.post("/user_api/mileageCharge", {"charge_cost":$("#charge_cost").val(), "charge_type":$("#charge_type").val()}, function (result) {
                if (result.success) {
                    alert("마일리지가 충전되었습니다.");
                    location.reload();
                } else {
                    alert(result.data.message);
                    location.reload();
                }
            });
        }
    });
}


function traderRating() {
    $(".btn-rating").on("click", function () {
        var rate_id = $(this).parent().find("input#target_id").val();
        var item_serial = $(this).parent().find("input#this_serial").val();

        $("#rate_id").val(rate_id);
        $("#item_serial").val(item_serial);
        $('#ratingModal').modal("show");
    });

    $("#btn-rateSubmit").on("click", function () {

        var isTrue = false;
        var data = $("#rateForm").serialize();
        var validate =
            {
                "item_serial":"평가를 할수 없습니다.",
                "rate_id":"평가를 할수 없습니다.",
                "memo":"거래평을 입력해주세요."
            };
        var validation = "";

        isTrue = confirm("고객평가를 하시겠습니까??");

        if(!$("input[name=rate]").val()) {
            alert("고객점수를 선택해 주세요.");
            isTrue = false;
            return false;
        }

        for(var key in validate) {
            if(!$("#"+key).val()) {
                alert(validate[key]);
                isTrue = false;
                return false;
            }
        }

        if(isTrue) {
            $.post("/user_api/traderRating", data, function (result) {
                if (result.success) {
                    alert("평가되었습니다.");
                    location.reload();
                } else {
                    alert(result.data.message);
                    location.reload();
                }
            });
        }
    });
}

$(document).ready(function(){
    addRatingWidget();

    mileageCharge();
    traderRating();

});