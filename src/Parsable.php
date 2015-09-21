<?php

namespace Flysap\ModuleManager;

interface Parsable {

    /**
     * Parse file .
     *
     * @param $file
     * @return mixed
     */
    public function parse($file);
}