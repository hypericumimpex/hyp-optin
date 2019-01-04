<?php
class Mpp_Infusionsoft_FunnelServiceBase extends Mpp_Infusionsoft_Service {

    public static function achieveGoal($integration, $callName, $contactId = 0, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $integration,
            $callName,
            (int) $contactId
        );

        return parent::send($app, "FunnelService.achieveGoal", $params);
    }

}