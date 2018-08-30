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
                <label class="px-1 mb-1" for="email"><strong>Name</strong></label>
                <input type="text" name="user" class="form-control" id="user" placeholder="Name to display" autofocus>
            </div>
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
            <div class="form-group mb-1 mt-4 pt-2">
                <input type="submit" name="register" class="btn btn-block btn-outline-primary btn-lg" id="submit" value="Sign up">
            </div>
        </form>
    </div>
</div>
