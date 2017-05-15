<article class="container">
    <div class="page-header">
        <h1>회원가입 <small>Register Form</small></h1>
    </div>
    <div class="col-md-12">
        <form id="registerForm" class="form-horizontal" method="post">
            <input type="hidden" id="idChk" value="N" />
            <div class="form-group">
                <label class="col-sm-4 control-label" for="user_id">아이디</label>
                <div class="col-sm-5 form-inline">
                    <input type="text" id="user_id" name="user_id" class="form-control required" data-label="아이디를" placeholder="아이디" engNumUnder="true">
                    <input type="button" id="idOverlapChk" class="btn btn-default" value="검색">
                    <p class="help-block">영문,숫자, 밑줄만 입력 가능</p>
                    <p id="ErrorTxt" class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="user_pwd">패스워드</label>
                <div class="col-sm-5">
                    <input id="user_pwd" name="user_pwd" type="password" class="form-control required" data-label="패스워드를" placeholder="패스워드">
                    <p class="help-block">영문 숫자 포함 6자 이상</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="pwdCheck">패스워드 확인</label>
                <div class="col-sm-5">
                    <input id="pwdCheck" name="pwdCheck" type="password" class="form-control required" data-label="패스워드를 확인을" placeholder="패스워드 확인">
                    <p class="help-block">패스워드를 한번 더 입력해주세요.</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="birthday">생년월일</label>
                <div class="col-sm-3">
                    <div id="div_datepicker" class="input-group date">
                        <input type="text" id="birthday" name="birthday" class="form-control required" data-label="생년월일을" placeholder="ex) 19900101" readonly/>
                        <div class = "input-group-addon" >
                            <span class = "glyphicon glyphicon-th" ></span >
                        </div>
                    </div >
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="inputName">이름</label>
                <div class="col-sm-5">
                    <input type="text" id="name"  name="name" class="form-control required" data-label="이름을" placeholder="이름">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label" for="inputName">주소</label>
                <div class="col-sm-5 serchAddr">
                    <div class="form-inline">
                        <input type="text" class="form-control" id="postcode" name="postcode" placeholder="우편번호" readonly>
                        <input type="button" id="search-postcode" class="btn btn-default" value="검색">
                    </div>
                    <div>
                        <input type="text" id="roadaddr" name="roadaddr" class="form-control required" data-label="주소를" placeholder="도로명주소">
                    </div>
                    <div>
                        <input type="text" id="addr" name="addr" class="form-control required" data-label="주소를" placeholder="지번주소">
                        <span id="guide" style="color:#999"></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12 text-center">
                    <button class="btn btn-primary" type="button" id="btn-submit">회원가입</button>
                    <a href="/user/login"><button class="btn btn-danger" type="button">가입취소</button></a>
                </div>
            </div>
        </form>
    </div>
</article>
<script src="http://dmaps.daum.net/map_js_init/postcode.v2.js"></script>