<?php
namespace App;
include('Processors/ProcessorFactory.php');
use App\Processors\ProcessorFactory;
use App\Exceptions\UnknownServiceException;

class Gitsha
{
    private array $arguments;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function run(): string
    {
        try {
            $processor = (new ProcessorFactory())->make(...$this->arguments);

            return $processor->process();
        } catch (UnknownServiceException $e) {
            return sprintf('Unknown service: %s', $this->arguments[2]);
        }
    }
}

