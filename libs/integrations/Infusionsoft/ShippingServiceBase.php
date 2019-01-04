<?php
class Mpp_Infusionsoft_ShippingServiceBase extends Mpp_Infusionsoft_Service{

    public static function getAllShippingOptions(Mpp_Infusionsoft_App $app = null){
        $params = array(
        );

        return parent::send($app, "ShippingService.getAllShippingOptions", $params);
    }

    public static function getAllConfiguredShippingOptions(Mpp_Infusionsoft_App $app = null){
        $params = array(
        );

        return parent::send($app, "ShippingService.getAllShippingOptions", $params);
    }

    public static function getFlatRateShippingOption($optionId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $optionId
        );

        return parent::send($app, "ShippingService.getFlatRateShippingOption", $params);
    }

    public static function getOrderTotalShippingOption($optionId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $optionId
        );

        return parent::send($app, "ShippingService.getOrderTotalShippingOption", $params);
    }

    public static function getOrderTotalShippingRanges($optionId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $optionId
        );

        return parent::send($app, "ShippingService.getOrderTotalShippingRanges", $params);
    }

    public static function getProductBasedShippingOption($optionId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $optionId
        );

        return parent::send($app, "ShippingService.getProductBasedShippingOption", $params);
    }

    public static function getProductShippingPricesForProductShippingOption($optionId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $optionId
        );

        return parent::send($app, "ShippingService.getProductShippingPricesForProductShippingOption", $params);
    }

    public static function getOrderQuantityShippingOption($optionId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $optionId
        );

        return parent::send($app, "ShippingService.getOrderQuantityShippingOption", $params);
    }

    public static function getWeightBasedShippingOption($optionId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $optionId
        );

        return parent::send($app, "ShippingService.getWeightBasedShippingOption", $params);
    }

    public static function getWeightBasedShippingRanges($optionId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $optionId
        );

        return parent::send($app, "ShippingService.getWeightBasedShippingRanges", $params);
    }

    public static function getUpsShippingOption($optionId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $optionId
        );

        return parent::send($app, "ShippingService.getUpsShippingOption", $params);
    }

}
