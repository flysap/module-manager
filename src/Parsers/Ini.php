<?php

namespace Flysap\ModuleManager\Parsers;

use Flysap\ModuleManger\Contracts\ConfigParserContract;

class Ini implements ConfigParserContract {

    /**
     * Parse file ..
     *
     * @param $file
     * @return array
     */
    public function parse($file) {
        return parse_ini_string(
            $file, true, INI_SCANNER_NORMAL
        );
    }
}