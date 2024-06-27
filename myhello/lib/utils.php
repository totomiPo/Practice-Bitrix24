<?php
namespace Bitrix\Myhello;

/*
 * if (CModule::IncludeModule('myhello')){
 *      Bitrix\Myhello\Utils :: hi();
 * }
 * */

class Utils {
    public  $param;
    static function hi()
    {
        echo "Привет мир!";
    }
}

?>