<?php

if (isset($this) && ($this instanceof szywo\TinyTweet\PageTemplate)) {

$this->httpResponseCode();

?>
<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="If you just can't save it for yourself">
    <meta name="viewport" content="width=devicewidth, initial-scale=1">
    <title><?= $this->getTitle(); ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= $this->basePath ?>css/default.css">
    <link rel="stylesheet" href="<?= ($this->basePath).($this->getAuxilaryCssFile()) ?>">
</head>
<body>
    <div class="container-fluid py-sm-5 py-3 ">

<?php
    $this->renderMenuBox();
    $this->renderErrorBox();
    $this->renderFormBox();
    $this->renderContentBox();
?>

    </div>
</body>
</html>

<?php

}
