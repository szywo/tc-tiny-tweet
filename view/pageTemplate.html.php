<!DOCTYPE html>
<html lang="pl-PL">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="If you just can't save it for yourself">
    <meta name="viewport" content="width=devicewidth, initial-scale=1">
    <title><?= $tpl_title??"Tiny Tweet" ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= $tpl_basePath ?>css/default.css">
    <?php if (isset($tpl_cssFile)) { ?>
        <link rel="stylesheet" href="<?= ($tpl_basePath)."css/".($tpl_cssFile) ?>">
    <?php } ?>
</head>
<body>
    <?= $tpl_bodyTemplate ?>
</body>
</html>
