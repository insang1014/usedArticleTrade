//한글만 onKeyPress="hangul();"  --IE호환성
function hangul(){
    if((event.keyCode < 12592) || (event.keyCode > 12687)){
        alert("한글만 입력이 가능합니다.");
        event.returnValue = false
    }
}

function numberWithCommas(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$(document).ready(function(){
//숫자만numberonly='true'
    $(document).on("keyup", "input:text[numberOnly]", function() {$(this).val( $(this).val().replace(/[^0-9]/gi,"") );});

//영문만engOnly='true'
    $(document).on("keyup", "input:text[engOnly]", function() {$(this).val( $(this).val().replace(/[0-9]|[^\!-z]/gi,"") );});

//영문 + 숫자 + 밑줄
    $(document).on("keyup", "input:text[engNumUnder]", function() {$(this).val( $(this).val().replace(/[^_a-z0-9]/gi,"") );});

//한글만korOnly='true' --테스트결과 IE에서 안먹을 때가 종종있음.
    $(document).on("keyup", "input:text[korOnly]", function() {$(this).val( $(this).val().replace(/[a-z0-9]|[ \[\]{}()<>?|`~!@#$%^&*-_+=,.;:\"\\]/g,"") );});
});