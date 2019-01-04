<?php
class Mpp_Infusionsoft_DiscountServiceBase extends Mpp_Infusionsoft_Service{

    public static function getOrderTotalDiscount($discountId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $discountId
        );

        return parent::send($app, "DiscountService.getOrderTotalDiscount", $params);
    }

}