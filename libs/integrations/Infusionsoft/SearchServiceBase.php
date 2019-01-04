<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Joey
 * Date: 6/25/11
 * Time: 4:26 PM
 * To change this template use File | Settings | File Templates.
 */

class Mpp_Infusionsoft_SearchServiceBase extends Mpp_Infusionsoft_Service {
    public static function getAllReportColumns($savedSearchId, $userId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $savedSearchId,
            (int) $userId,
        );

        return parent::send($app, "SearchService.getAllReportColumns", $params);
    }

    public static function getSavedSearchResults($savedSearchId, $userId, $pageNumber, array $returnFields, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $savedSearchId,
            (int) $userId,
            (int) $pageNumber,
            $returnFields
        );

        return parent::send($app, "SearchService.getSavedSearchResults", $params);
    }

    public static function getSavedSearchResultsAllFields($savedSearchId, $userId, $pageNumber, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $savedSearchId,
            (int) $userId,
            (int) $pageNumber,
        );

        return parent::send($app, "SearchService.getSavedSearchResultsAllFields", $params);
    }

    public static function getAvailableQuickSearches($userId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $userId,
        );

        return parent::send($app, "SearchService.getAvailableQuickSearches", $params);
    }

    public static function quickSearch($quickSearchType, $userId, $searchData, $page, $returnLimit, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (string) $quickSearchType,
            (int) $userId,
            (string) $searchData,
            (int) $page,
            (int) $returnLimit
        );

        return parent::send($app, "SearchService.quickSearch", $params);
    }

    public static function getDefaultQuickSearch($userId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $userId,
        );

        return parent::send($app, "SearchService.getDefaultQuickSearch", $params);
    }

    public static function getSavedSearchIdFromName($savedSearchName){
        $results = Mpp_Infusionsoft_DataService::query(new Mpp_Infusionsoft_SavedFilter(), array('FilterName' => $savedSearchName));
        if(count($results) > 0){
            return array_shift($results);
        } else {
            return false;
        }
    }
}
