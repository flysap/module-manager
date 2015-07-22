<?php

namespace Flysap\ModuleManager;

class ParserFactory {

    /**
     * Parser factory .
     *
     * @param $slug
     * @return mixed
     */
    public static function factory($slug) {
        if( class_exists('Flysap\ModuleManager\Parsers\\' .ucfirst($slug)) ) {
            $class = 'Flysap\ModuleManager\Parsers\\' .ucfirst($slug);

            return new $class;
        }
    }
}
