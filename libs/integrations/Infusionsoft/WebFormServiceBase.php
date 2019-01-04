<?php
class Mpp_Infusionsoft_WebFormServiceBase extends Mpp_Infusionsoft_Service{

    public static function getMap(Mpp_Infusionsoft_App $app = null){
        $params = array(
        );

        return parent::send($app, "WebFormService.getMap", $params);
    }

    public static function getHTML($webformId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $webformId
        );

        return parent::send($app, "WebFormService.getHTML", $params);
    }

}