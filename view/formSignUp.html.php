<div class="row justify-content-center">
    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-4 col-xl-3">
        <div class="card">
            <div class="card-header">
                <ul id="logregnav" class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $tpl_signInUri ?>">Sign in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= $tpl_signUpUri ?>">Sign up</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form action="<?= $tpl_signUpUri ?>" accept-charset="UTF-8" method="post">
                    <div class="form-group mb-4">
                        <label class="px-1 mb-1" for="email"><strong>Email address</strong></label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="Enter email">
                    </div>
                    <div class="form-group mb-4">
                        <label class="px-1 mb-1" for="pass"><strong>Password</strong></label>
                        <input type="password" name="pass" class="form-control" id="pass" placeholder="Enter password">
                    </div>
                    <div class="form-group mb-4">
                        <label class="px-1 mb-1" for="pass"><strong>Repeat password</strong></label>
                        <input type="password" name="pass2" class="form-control" id="pass2" placeholder="Repeat password">
                    </div>
                    <div class="form-group mb-4">
                        <label class="px-1 mb-1" for="email"><strong>Nick name</strong></label>
                        <input type="email" name="user" class="form-control" id="user" placeholder="Nickname to display">
                    </div>
                    <div class="form-group mb-1 mt-4 pt-2">
                        <input type="submit" name="register" class="btn btn-block btn-outline-primary btn-lg" id="submit" value="Sign up">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
