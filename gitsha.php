<?php

if ($argc <= 1) {
    echo 'Please provide repository name that you want to scan as an argument.';
    die;
}

$ch = curl_init();
$endpoint = sprintf('https://api.github.com/repos/%s/commits', $argv[1]);

if (isset($argv[2])) {
    $endpoint .= sprintf('?sha=%s', $argv[2]);
}

curl_setopt_array(
    $ch,
    [
        CURLOPT_HTTPHEADER => [
            'User-Agent: marcinsola',
        ],
        CURLOPT_URL => $endpoint,
        CURLOPT_RETURNTRANSFER => true,
    ]
);

$result = json_decode(curl_exec($ch));
curl_close($ch);

if (is_array($result)) {
    echo $result[0]->sha;
    die;
}

echo sprintf("Couldn't find repo %s. Please make sure that the repo and branch names provided are valid.", $argv[1]);
