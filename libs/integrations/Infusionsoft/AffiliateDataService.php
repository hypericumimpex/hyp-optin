<?php
/**
 * Created by JetBrains PhpStorm.
 * User: prescott
 * Date: 5/13/13
 * Time: 11:22 AM
 * To change this template use File | Settings | File Templates.
 */


class Mpp_Infusionsoft_AffiliateDataService extends Mpp_Infusionsoft_Service {
    static $earliest_month_to_query = null;
    static $affiliates_cache = array();
    static $orderByField = 'DateEarned';
    //'DateEarned' is the only field for which ordering is supported.
    //If month is 0, all commissions for the current month will be returned
    public static function queryWithOrderBy($object, $queryData, $orderByField, $ascending = true, $limit = 1000, $month = 0, $returnFields = false, Mpp_Infusionsoft_App $app = null){
        //Set a hard date for earliest_month_to_query so that we don't get into infinite loops trying to find commissions in the 1700's (this happens when you sync an empty application)
        if ($month == 0){
            $firstOrder = Mpp_Infusionsoft_DataService::queryWithOrderBy(new Mpp_Infusionsoft_Job(), array('Id' => '%'), 'Id', true, 1);
            if (!empty($firstOrder)){
                self::$earliest_month_to_query = $firstOrder[0]->DateCreated;
            }
        }
        if (empty(self::$earliest_month_to_query)){
            self::$earliest_month_to_query = '2013-01-01';
        }

        //Calculate beginning of this month
        $startDate = date('Y-m-01 00:00:00', strtotime(" - $month months"));
        $startDate = date(Mpp_Infusionsoft_Service::apiDateFormat, strtotime($startDate));

        $endDate = date('Y-m-t 23:59:59', strtotime($startDate));
        $endDate = date(Mpp_Infusionsoft_Service::apiDateFormat, strtotime($endDate));

        //get an array of all affiliates
        $affiliates = self::$affiliates_cache;
        if (empty($affiliates)){
            $page = 0;
            do {
                $affiliatesPage = Mpp_Infusionsoft_DataService::query(
                    new Mpp_Infusionsoft_Affiliate(),
                    array('Id' => '%'),
                    1000,
                    $page,
                    array('Id'),
                    $app
                );
                $page += 1;
                $affiliates = array_merge($affiliates, $affiliatesPage);
            } while (sizeof($affiliatesPage) >= 1000);
        }

        //Now get all of the commissions from each one
        $objects = array();
        while (count($objects) < $limit && $startDate >= self::$earliest_month_to_query){
            foreach ($affiliates as $affiliate) {
                if (get_class($object) == 'Mpp_Infusionsoft_Commission'){
                    $objects = array_merge(
                        $objects,
                        Mpp_Infusionsoft_APIAffiliateService::affCommissions($affiliate->Id, $startDate, $endDate, $app)
                    );
                } elseif (get_class($object) == 'Mpp_Infusionsoft_Clawback'){
                    $objects = array_merge(
                        $objects,
                        Mpp_Infusionsoft_APIAffiliateService::affClawbacks($affiliate->Id, $startDate, $endDate, $app)
                    );
                }
            }
            $startDate = date('Y-m-d', strtotime($startDate . '- 1 month'));
            $endDate = date('Y-m-t 23:59:59', strtotime($startDate));
            $startDate = date(Mpp_Infusionsoft_Service::apiDateFormat, strtotime($startDate));
            $endDate = date(Mpp_Infusionsoft_Service::apiDateFormat, strtotime($endDate));
        }


        self::$orderByField = $orderByField;
        usort($objects, array('Mpp_Infusionsoft_AffiliateDataService', 'sortCommissions'));
        return $objects;
    }

    public static function sortCommissions($a, $b){
        return $a->{self::$orderByField} < $b->{self::$orderByField};
    }
}