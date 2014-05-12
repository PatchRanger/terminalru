<?php

$app = require __DIR__.'/app.php';

// Fill in the database with default content.
$app->defaultContent();
echo "Default content successfully created!\n";
return $app;