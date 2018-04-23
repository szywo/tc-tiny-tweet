<div class="row justify-content-center mb-3 mb-sm-5">
    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-4 col-xl-3">
        <div class="card">
            <div class="card-header">
                <ul id="logregnav" class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?= $tpl_signInUri ?>">Sign in</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $tpl_signUpUri ?>">Sign up</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form action="<?= $tpl_signInUri ?>" accept-charset="UTF-8" method="post">
                    <div class="form-group mb-4">
                        <label class="px-1 mb-1" for="email"><strong>Email address</strong></label>
                        <input type="email" name="email" class="form-control" id="email" placeholder="email">
                    </div>
                    <div class="form-group mb-4">
                        <label class="px-1 mb-1" for="pass"><strong>Password</strong></label>
                        <input type="password" name="pass" class="form-control" id="pass" placeholder="password">
                    </div>
                    <div class="form-group mb-1 mt-4 pt-2">
                        <input type="submit" name="login" class="btn btn-block btn-outline-primary btn-lg" id="submit" value="Sign in">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
