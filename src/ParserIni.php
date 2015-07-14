<?php

namespace Flysap\ModuleManger;

use Flysap\ModuleManger\Contracts\ConfigParserContract;

class ParserIni implements ConfigParserContract {

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