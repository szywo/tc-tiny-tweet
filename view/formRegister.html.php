<div class="card">
    <div class="card-header">
        <ul id="logregnav" class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link" href="<?= $tpl_basePath.$tpl_loginUri ?>">Sign in</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="<?= $tpl_basePath.$tpl_registerUri ?>">Sign up</a>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <form action="<?= $tpl_basePath.$tpl_registerUri ?>" accept-charset="UTF-8" method="post">
            <div class="form-group mb-4">
                <label class="px-1 mb-1" for="name"><strong>Name</strong></label>
                <input type="text" name="name" class="form-control <?= $tpl_validate?($tpl_errorValidName?"is-invalid":"is-valid"):"" ?>" id="name" placeholder="Name to display" value="<?= $tpl_registerName??"" ?>" autofocus>
                <?= $tpl_nameValidMsg?? "" ?>
                <?= $tpl_errorValidNameMsg?? "" ?>
            </div>
            <div class="form-group mb-4">
                <label class="px-1 mb-1" for="email"><strong>Email address</strong></label>
                <input type="email" name="email" class="form-control <?= $tpl_validate?($tpl_errorValidEmail?"is-invalid":"is-valid"):"" ?>" id="email" placeholder="Enter email" value="<?= $tpl_registerEmail??"" ?>">
                <?= $tpl_emailValidMsg?? "" ?>
                <?= $tpl_errorValidEmailMsg?? "" ?>
            </div>
            <div class="form-group mb-4">
                <label class="px-1 mb-1" for="pass"><strong>Password</strong></label>
                <input type="password" name="pass" class="form-control <?= $tpl_validate?($tpl_errorValidPass?"is-invalid":"is-valid"):"" ?>" id="pass" placeholder="Enter password">
                <?= $tpl_errorValidPassMsg?? "" ?>
            </div>
            <div class="form-group mb-4">
                <label class="px-1 mb-1" for="pass"><strong>Confirm password</strong></label>
                <input type="password" name="pass2" class="form-control <?= $tpl_validate?($tpl_errorValidPass2?"is-invalid":"is-valid"):"" ?>" id="pass2" placeholder="Repeat password">
                <?= $tpl_errorValidPass2Msg?? "" ?>
            </div>
            <div class="form-group mb-1 mt-4 pt-2">
                <input type="submit" name="register" class="btn btn-block btn-outline-primary btn-lg" id="submit" value="Sign up">
            </div>
        </form>
    </div>
</div>
