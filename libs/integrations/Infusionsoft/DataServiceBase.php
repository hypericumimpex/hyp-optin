<?php
class Mpp_Infusionsoft_DataServiceBase extends Mpp_Infusionsoft_Service{

    public static function authenticateUser($username, $passwordHash, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $username,
            $passwordHash
        );
        return parent::send($app, "DataService.authenticateUser", $params);
    }

}