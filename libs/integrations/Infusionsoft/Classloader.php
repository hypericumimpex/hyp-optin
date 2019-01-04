<?php

/* Usage */

class Mpp_Infusionsoft_Classloader
{

    protected $paths = array();

    public function __construct()
    {
        if (defined("INFUSIONSOFT_SDK_TEST")) {
            $path = dirname(dirname(__FILE__)) . "/TestMocks/";
            $this->paths[] = $path;
        }
        $path = dirname(dirname(__FILE__)) . "/";
        $this->paths[] = $path;
    }

    public function loadClass($className)
    {
        if (strpos($className, "Mpp_Infusionsoft_") === 0) {
            $className = preg_replace('/[^a-zA-Z0-9_]/s', '', $className);
            $className = str_replace('Mpp_', "", $className);//Agregué esto por el prefijo de MasterPopups
            $relativePath = str_replace('_', "/", $className) . ".php";
            foreach ($this->paths as $path) {
                if (file_exists($path . $relativePath)) {
                    include($path . $relativePath);
                    return true;
                }
            }
        }
        return false;
    }

    protected function addPath($path)
    {
        $paths[] = $path;
    }

}
