function submitFunc(){
    $("#btn-submit").on("click", function(e) {
        e.preventDefault();

        var isTrue = true;

        if($("#user_id").val()){
            if($("#idChk").val() == "N") {
                alert("아이디 중복체크를 해주세요.");
                isTrue = false;
                return false;
            }
        }

        if(isTrue == true) {
            if ($("#user_pwd").val() && $("#pwdCheck").val()) {
                if ($("#user_pwd").val() != $("#pwdCheck").val()) {
                    alert("패스워드를 확인해주시기바립니다.");
                    isTrue = false;
                    return false;
                }
            }
        }

        // form 필수 입력 공란 검증
        $(".required").each(function() {
            var input = $(this);
            if (!input.val()) {
                alert(input.data("label") + " 입력해주세요.");
                input.focus();
                isTrue = false;
                return false;
            }
        });

        var data = $("#registerForm").serializeArray();
        if(isTrue == true)
        {
            $.post("/user_api/userRegister", {"param" : data}, function (result) {
                if (result.success) {
                    alert("회원가입 되었습니다.\n\n로그인 후 이용해 주세요.");
                    location.href = "/user/login"
                } else {
                    alert(result.data.message);
                    location.href = "/user/register"
                }
            });
        }
    });
}

function overlapChkFunc(){
    $("#idOverlapChk").on('click', function(e) {
        e.preventDefault();

        if($("#user_id").val() == "") {
            alert("아이디를 입력(등록)해주세요.");
            $("#user_id").focus();
            return false;
        }

        $.post("/user_api/idOverlapCheck", {"user_id":$("#user_id").val()}, function (result) {
            if (result.success) {
                $("#ErrorTxt").show().text("사용 가능한 아이디입니다").css("color", "#0000FF");
                $("#idChk").val("Y");
            } else {
                $("#ErrorTxt").show().text("사용할 수 없는 아이디 입니다.").css("color", "#FF0000");
                $("#idChk").val("N");
            }
        });
    });
}

function execDaumPostcode() {
    $("#search-postcode").on("click", function (e) {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 도로명 주소의 노출 규칙에 따라 주소를 조합한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var fullRoadAddr = data.roadAddress; // 도로명 주소 변수
                var extraRoadAddr = ''; // 도로명 조합형 주소 변수

                // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                    extraRoadAddr += data.bname;
                }
                // 건물명이 있고, 공동주택일 경우 추가한다.
                if(data.buildingName !== '' && data.apartment === 'Y'){
                    extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 도로명, 지번 조합형 주소가 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                if(extraRoadAddr !== ''){
                    extraRoadAddr = ' (' + extraRoadAddr + ')';
                }
                // 도로명, 지번 주소의 유무에 따라 해당 조합형 주소를 추가한다.
                if(fullRoadAddr !== ''){
                    fullRoadAddr += extraRoadAddr;
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                document.getElementById('postcode').value = data.zonecode; //5자리 새우편번호 사용
                document.getElementById('roadaddr').value = fullRoadAddr;
                document.getElementById('addr').value = data.jibunAddress;

                // 사용자가 '선택 안함'을 클릭한 경우, 예상 주소라는 표시를 해준다.
                if(data.autoRoadAddress) {
                    //예상되는 도로명 주소에 조합형 주소를 추가한다.
                    var expRoadAddr = data.autoRoadAddress + extraRoadAddr;
                    document.getElementById('guide').innerHTML = '(예상 도로명 주소 : ' + expRoadAddr + ')';

                } else if(data.autoJibunAddress) {
                    var expJibunAddr = data.autoJibunAddress;
                    document.getElementById('guide').innerHTML = '(예상 지번 주소 : ' + expJibunAddr + ')';

                } else {
                    document.getElementById('guide').innerHTML = '';
                }
            }
        }).open();
    });
}

$(document).ready(function(){
    submitFunc();
    execDaumPostcode();
    overlapChkFunc();

    // 선언한 TextBox에 DateTimePicker 위젯을 적용한다.
    $.fn.datepicker.dates['kr'] = {
        days: ["일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일", "일요일"],
        daysShort: ["일", "월", "화", "수", "목", "금", "토", "일"],
        daysMin: ["일", "월", "화", "수", "목", "금", "토", "일"],
        months: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        monthsShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        titleFormat: "yyyy MM"
    };

    $("#div_datepicker").datepicker({
        language: "kr",
        format: 'yyyymmdd',
        endDate: 'today',
        autoclose: true,
        todayHighlight: true,
        defaultViewDate:'1990-01-01'
    }).on('changeDate', function(e) {

    });
});