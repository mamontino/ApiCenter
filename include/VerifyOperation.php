<?php

class verifyOperation
{
    private $con;

    function __construct()
    {
        require_once dirname(__FILE__) . '/DbConnect.php';
        require_once dirname(__FILE__) . '/config.php';
        $db = new dbConnect();
        $this->con = $db->connect();
    }

    /**
     * Проверка валидности Api ключа
     * Ключ должен храниться в config.php
     * @param $api_key
     * @return boolean
     */
    public function isHaveAccess($api_key)
    {
        if ($api_key == API_KEY) {
            return true;
        } else {
            return false;
        }
    }
}