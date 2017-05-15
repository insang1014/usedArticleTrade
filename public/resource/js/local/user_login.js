function submitFunc(){
    $("#btn-submit").on("click", function(e) {
        e.preventDefault();

        var isTrue = true;

        // form 필수 입력 공란 검증
        $(".required").each(function() {
            var input = $(this);
            if (!input.val()) {
                alert(input.data("label") + " 입력(등록)해주세요.");
                input.focus();
                isTrue = false;
                return false;
            }
        });

        if(isTrue == true)
        {
            $.post("/user_api/userLogin/", {"user_id":$("#user_id").val(), "user_pwd":$("#user_pwd").val()}, function (result) {
                if (result.success) {
                    location.href = "/index"
                } else {
                    $("#ErrorTxt").show().text(result.data.message).css("color", "#FF0000");
                }
            });
        }
    });
}

$(document).ready(function(){
    submitFunc();
});