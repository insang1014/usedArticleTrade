// ADD RATING WIDGET
function addRatingWidget() {
    var trader_id = $("#traderId").text();

    var ratingElement = document.querySelector(".c-rating")
    var currentRating = 0;
    var maxRating = 5;
    //var callback = function() {}

    $.ajax({
        type: "post",
        url: "/item_api/getRatingAvg",
        data: {rate_id:trader_id},
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

function fncTradeOffer(isTrue, mode) {
    if(isTrue) {
        $.post("/item_api/tradeOffer/", {"item_serial":$("#serial").val(), offer_cost:$("#offer_cost").val(), mode:mode}, function (result) {
            if (result.success) {
                alert("등록되었습니다.\n\n마이페이지에서 거래진행상태를\n\n확인해주세요.");
                location.href = "/user/mypage";
            } else {
                if(result.data.message == "M") {
                    alert("보유 마일리지가 부족합니다.\n\n마일리지를 충전해주세요.");
                    location.href = "/user/mypage";
                } else {
                    alert(result.data.message);
                    location.reload();
                }
            }
        });
    }
}

$(document).ready(function(){

    addRatingWidget();

    $("#btn-propose").on("click", function () {

        var isTrue = false;

        if(!$("#offer_cost").val()) {
            alert("금액을 입력해 주세요.");
            $("#offer_cost").focus();
            return false;
        }

        $.post("/item_api/tradeOffer/true", {"item_serial":$("#serial").val()}, function (result) {
            if (result.success) {
                isTrue = confirm("거래를 제안하시겠습니까?")
                fncTradeOffer(isTrue, "insert");
            } else {
                isTrue = confirm("이미 제안한 거래입니다.\n\n거래금액을 수정하시겠습니까?");
                fncTradeOffer(isTrue, "update");
            }
        });
    });

   $("#btn-submit").on("click", function () {
       $('#mySmallModal').modal("show");
    });
});
