<?php
require('src/Gitsha.php');

use App\Gitsha;

if (sizeof($argv) <= 1) {
    return 'Please provide repository name that you want to scan as an argument.';
}
$command = new Gitsha(array_slice($argv, 1));
echo $command->run();
