<?php
/**
* Homepage template.
*/
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Drupal Camp</title>
        <link rel="stylesheet" href="../css/main.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    </head>
    <body class="important-dates-timeline">
<?php
include "modules/header.php";
?>

<div class="section-intro">
    <div class="section-intro--background"></div>
    <div class="section-intro--text">
        <h1 class="section-intro--title">Important dates</h1>
    </div>
</div>

<?php
include "modules/important-dates.php";

include "modules/footer.php";
include "inc/footer.inc.php";
?>
