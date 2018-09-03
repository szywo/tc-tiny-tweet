<div class="card">
    <div class="card-header">
        <ul id="logregnav" class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="<?= $tpl_basePath.$tpl_loginUri ?>">Sign in</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $tpl_basePath.$tpl_registerUri ?>">Sign up</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <form action="<?= $tpl_basePath.$tpl_loginUri ?>" accept-charset="UTF-8" method="post">
            <div class="form-group mb-4">
                <label class="px-1 mb-1" for="email"><strong>Email address</strong></label>
                <input type="email" name="email" class="form-control <?= isset($tpl_authInvalid)?"is-invalid":"" ?>" id="email" placeholder="email" value="<?= $tpl_userEmail??"" ?>" <?= isset($tpl_userEmail)?"":"autofocus" ?> required>
            </div>
            <div class="form-group mb-4">
                <label class="px-1 mb-1" for="pass"><strong>Password</strong></label>
                <input type="password" name="pass" class="form-control <?= isset($tpl_authInvalid)?"is-invalid":"" ?>" id="pass" placeholder="password" <?= isset($tpl_userEmail)?"autofocus":"" ?> required>
            </div>
            <div class="form-group mb-1 mt-4 pt-2">
                <input type="submit" name="login" class="btn btn-block btn-outline-primary btn-lg" id="submit" value="Sign in">
            </div>
        </form>
    </div>
</div>
