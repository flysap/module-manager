<?php

namespace Flysap\ModuleManager\Parsers;

use Flysap\ModuleManager\Contracts\ConfigParserContract;

class Json implements ConfigParserContract {

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