<?php

namespace App\Processors;

interface ProcessorInterface
{
    public function process(): string;
}