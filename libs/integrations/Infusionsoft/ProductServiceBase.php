<?php
class Mpp_Infusionsoft_ProductServiceBase extends Mpp_Infusionsoft_Service{

    public static function deactivateCreditCard($creditCardId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $creditCardId
        );
        return parent::send($app, "ProductService.deactivateCreditCard", $params);
    }
}