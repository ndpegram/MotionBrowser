<?php

////////////////////////////////////////////////////////////////////////////////
interface formatUtils {

    const FORMAT_TEXT = 0;
    const FORMAT_HTML = 1;

    static function createFormatter (int $type) ;
}
