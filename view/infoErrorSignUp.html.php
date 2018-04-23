<div class="row justify-content-center">
    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-4 col-xl-3">
        <div class="alert alert-danger" role="alert">
            Registration failed.
            <ul>
            <?php foreach ($tpl_errorMessages as $message) { ?>
                <li><?= $message ?></li>
            <?php } ?>
        </ul>
        </div>
    </div>
</div>
