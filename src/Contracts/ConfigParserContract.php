<?php

namespace Flysap\ModuleManager\Contracts;

interface ConfigParserContract {

    /**
     * Parse file .
     *
     * @param $file
     * @return mixed
     */
    public function parse($file);
}