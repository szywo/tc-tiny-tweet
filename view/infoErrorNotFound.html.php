<div class="row justify-content-center">
    <div class="col-xs-12 col-sm-10 col-md-8 col-lg-6 col-xl-4">
        <div class="card border-danger text-center text-danger">
            <h4 class="card-header bg-danger text-white border-danger">
                Error 404
            </h4>
            <div class="card-body">
                <p class="card-text">
                    Requested page "/<?= $tpl_requestUri ?>" was not found on this server.
                </p>
                <p class="card-text">
                    Please check address again or start over
                    <strong><a class="text-danger" href="<?= $tpl_basePath ?>">here</a></strong>.
                </p>
            </div>
        </div>
    </div>
</div>
