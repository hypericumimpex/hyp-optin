<?php
class Mpp_Infusionsoft_ContactServiceBase extends Mpp_Infusionsoft_Service{

    public static function add($data, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $data
        );

        return parent::send($app, "ContactService.add", $params);
    }

    public static function load($id, $selectedFields, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $id,
            $selectedFields
        );

        return parent::send($app, "ContactService.load", $params);
    }

    public static function merge($contactId, $duplicateContactId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $duplicateContactId
        );

        return parent::send($app, "ContactService.merge", $params);
    }

    public static function update($contactId, $data, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            $data
        );

        return parent::send($app, "ContactService.update", $params);
    }

    public static function addWithDupCheck($data, $dupCheckType, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $data,
            $dupCheckType
        );

        return parent::send($app, "ContactService.addWithDupCheck", $params);
    }

    public static function addToCampaign($contactId, $campaignId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $campaignId
        );

        return parent::send($app, "ContactService.addToCampaign", $params);
    }

    public static function addToGroup($contactId, $groupId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $groupId
        );

        return parent::send($app, "ContactService.addToGroup", $params, null, true);
    }

    public static function getAppSetting($hash, $module, $param, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $hash,
            $module,
            $param
        );

        return parent::send($app, "ContactService.getAppSetting", $params);
    }

    public static function getAppSettingInt($hash, $module, $param, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $hash,
            $module,
            $param
        );

        return parent::send($app, "ContactService.getAppSettingInt", $params);
    }

    public static function linkContact($remoteApp, $remoteId, $localId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $remoteApp,
            (int) $remoteId,
            (int) $localId
        );

        return parent::send($app, "ContactService.linkContact", $params);
    }

    public static function locateContactLink($locateMapId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $locateMapId
        );

        return parent::send($app, "ContactService.locateContactLink", $params);
    }

    public static function markLinkUpdated($locateMapId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $locateMapId
        );

        return parent::send($app, "ContactService.markLinkUpdated", $params);
    }

    public static function pauseCampaign($contactId, $campaignId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $campaignId
        );

        return parent::send($app, "ContactService.pauseCampaign", $params);
    }

    public static function refreshApp($hash, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $hash
        );

        return parent::send($app, "ContactService.refreshApp", $params);
    }

    public static function removeFromCampaign($contactId, $campaignId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $campaignId
        );

        return parent::send($app, "ContactService.removeFromCampaign", $params);
    }

    public static function removeFromGroup($contactId, $groupId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $groupId
        );

        return parent::send($app, "ContactService.removeFromGroup", $params);
    }

    public static function resumeCampaignForContact($contactId, $campaignId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $campaignId
        );

        return parent::send($app, "ContactService.resumeCampaignForContact", $params);
    }

    public static function runActionSequence($contactId, $actionSequenceId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $actionSequenceId
        );

        return parent::send($app, "ContactService.runActionSequence", $params);
    }

    public static function rescheduleCampaignStep($contactId, $campaignStepId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $contactId,
            (int) $campaignStepId
        );

        return parent::send($app, "ContactService.rescheduleCampaignStep", $params);
    }

    public static function getNextCampaignStep($contactId, $campaignId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $campaignId
        );

        return parent::send($app, "ContactService.getNextCampaignStep", $params);
    }

    public static function findByEmail($email, $selectedFields, Mpp_Infusionsoft_App $app = null){
        $params = array(
            $email,
            $selectedFields
        );

        return parent::send($app, "ContactService.findByEmail", $params);
    }

    public static function submitSurveyAndApplyActionSets($surveyResultId, $actionSetIds, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $surveyResultId,
            $actionSetIds
        );

        return parent::send($app, "ContactService.submitSurveyAndApplyActionSets", $params);
    }

    public static function getCampaigneeDetails($contactId, $campaignId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $campaignId
        );

        return parent::send($app, "ContactService.getCampaigneeDetails", $params);
    }

    public static function getCampaigneeStepDetails($contactId, $stepId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $stepId
        );

        return parent::send($app, "ContactService.getCampaigneeStepDetails", $params);
    }

    public static function getCampaignStepDetails($stepId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $stepId
        );

        return parent::send($app, "ContactService.getCampaignStepDetails", $params);
    }

    public static function getCampaignStepOrder($campaignId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $campaignId
        );

        return parent::send($app, "ContactService.getCampaignStepOrder", $params);
    }

    public static function getActivityHistoryTemplateMap(Mpp_Infusionsoft_App $app = null){
        $params = array(
        );

        return parent::send($app, "ContactService.getActivityHistoryTemplateMap", $params);
    }

    public static function applyActivityHistoryTemplate($contactId, $historyId, $userId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId,
            (int) $historyId,
            (int) $userId
        );

        return parent::send($app, "ContactService.applyActivityHistoryTemplate", $params);
    }


    public static function linkContacts($contactId1, $contactId2, $contactLinkTypeId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId1,
            (int) $contactId2,
            (int) $contactLinkTypeId
        );

        return parent::send($app, "ContactService.linkContacts", $params);
    }

    public static function unlinkContacts($contactId1, $contactId2, $contactLinkTypeId, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId1,
            (int) $contactId2,
            (int) $contactLinkTypeId
        );

        return parent::send($app, "ContactService.linkContacts", $params);
    }

    public static function listLinkedContacts($contactId1, Mpp_Infusionsoft_App $app = null){
        $params = array(
            (int) $contactId1,
        );

        return parent::send($app, "ContactService.listLinkedContacts", $params);
    }
}
