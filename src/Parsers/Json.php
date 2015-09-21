<?php

namespace Flysap\ModuleManager\Parsers;

use Flysap\ModuleManager\Parsable;

class Json implements Parsable {

    /**
     * Parse file ..
     *
     * @param $contents
     * @return array
     */
    public function parse($contents) {
        return json_decode($contents, true);
    }
}