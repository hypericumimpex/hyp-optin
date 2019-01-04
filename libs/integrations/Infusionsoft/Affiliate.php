<?php
class Mpp_Infusionsoft_Affiliate extends Mpp_Infusionsoft_Generated_Affiliate{
    var $customFieldFormId = -3;
    public function __construct($id = null, $app = null){
    	parent::__construct($id, $app);
    }

    /*
    public function getCommissions($startDate, $endDate) {
        $commissionData = Mpp_Infusionsoft_APIAffiliateService::affCommissions($this->Id, $startDate, $endDate);

        foreach ($commissionData)
    }
    */
}

