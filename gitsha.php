<?php

if ($argc <= 1) {
    echo 'Please provide repository name that you want to scan as an argument.';
    die;
}

$ch = curl_init();
curl_setopt_array(
    $ch,
    [
        CURLOPT_HTTPHEADER => [
            'User-Agent: marcinsola',
        ],
        CURLOPT_URL => sprintf('https://api.github.com/repos/%s/commits', $argv[1]),
        CURLOPT_RETURNTRANSFER => true,
    ]
);

$result = json_decode(curl_exec($ch));
curl_close($ch);

if (is_array($result)) {
    echo $result[0]->sha;
    die;
}

echo sprintf("Couldn't find repo %s. Please make sure that the data provided is valid.", $argv[1]);
