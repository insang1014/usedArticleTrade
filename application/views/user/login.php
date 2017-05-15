<div class="container">
    <div class="page-header">
        <h1> 로그인 <small>Login</small></h1>
    </div>
    <div class="form-group card card-container">
        <form id="loginForm" name="loginForm" method="post">
            <div class="form-group text-center">
                <input type="text" id="user_id" name="user_id" class="form-control required" data-label="아이디를" placeholder="ID" />
            </div>
            <div class="form-group text-center">
                <input type="password" id="user_pwd" name="user_pwd" class="form-control required" data-label="패스워드를" placeholder="Password" required/>
            </div>
            <p id="ErrorTxt" class="help-block"></p>
            <div class="text-center">
                <input type="submit" id="btn-submit" class="btn btn-primary btn-signin" value="로그인" />
            </div>
        </form><!-- /form -->
    </div><!-- /card-container -->
</div><!-- /container -->
