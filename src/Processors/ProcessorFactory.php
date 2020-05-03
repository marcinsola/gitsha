<?php

namespace App\Processors;
require('src/Exceptions/UnknownServiceException.php');
require('GithubProcessor.php');

spl_autoload_register(function($class) {
    require $class.'.php';
});

use App\Exceptions\UnknownServiceException;

class ProcessorFactory
{
    public function make(string $repo, ?string $branch = null, ?string $service = 'github'): ProcessorInterface
    {
        switch ($service) {
            case 'github':
                return new GithubProcessor($repo, $branch);
                break;
            default:
                throw new UnknownServiceException();
                break;
        }
    }
}
