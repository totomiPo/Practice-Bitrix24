<?php
namespace Bitrix\Myhello;

/*
 * if (CModule::IncludeModule('myhello')){
 *      Bitrix\Myhello\Utils :: hi();
 * }
 * */

class Utils {
    public  $param;

    public function hi()
    {
        echo "Привет мир!"
    }
}

?>