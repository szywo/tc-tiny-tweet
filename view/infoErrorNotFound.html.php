<div class="row justify-content-center">
    <div class="col-xs-12 col-sm-8 col-md-6 col-lg-4 col-xl-3">
        <div class="card border-danger text-center text-danger rounded-0">
            <h4 class="card-header bg-danger text-white border-danger rounded-0">
                Error 404
            </h4>
            <div class="card-body">
                <p class="card-text">
                    Requested page "<?= $tpl_requestUri ?>" was not found on this server.
                </p>
                <p class="card-text">
                    Please check address again or start over
                    <strong><a class="text-danger" href="<?= $tpl_basePath ?>">here</a></strong>.
                </p>
            </div>
        </div>
    </div>
</div>
