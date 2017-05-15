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

$(document).ready(function(){

    addRatingWidget();

    $("#btn-submit").on("click", function () {

        var isTrue = false;
        var data = $("#tradeForm").serialize();

        isTrue = confirm("거래를 제안하시겠습니까?");

        if(isTrue) {
            $.post("/trade_api/approveTrade", data, function (result) {
                if (result.success) {
                    alert("승인처리 되었습니다.\n\n마이페이지에서 진행상태를 확인해주세요.");
                } else {
                    alert(result.data.message);
                }
            });
        }
    });
});