<?php
class Mpp_Infusionsoft_CreditCardSubmissionService extends Mpp_Infusionsoft_Service{

    /**
     * @param $contactId
     * @param $successUrl
     * @param $failureUrl
     * @param Mpp_Infusionsoft_App $app
     * @return array|bool|mixed
     * NOTE: POST the credit card data to this URL: https://appnamehere.infusionsoft.com/app/creditCardSubmission/addCreditCard
     */
    public static function requestCcSubmissionToken($contactId, $successUrl, $failureUrl, Mpp_Infusionsoft_App $app = null){

        $params = array(
            (int) $contactId,
            $successUrl,
            $failureUrl,
        );

        return parent::send($app, "CreditCardSubmissionService.requestSubmissionToken", $params);
    }

    /**
     * @param Mpp_Infusionsoft_App $app
     * @return string
     */
    public static function getUrl(Mpp_Infusionsoft_App $app = null){
        if ($app == null){
            $app = Mpp_Infusionsoft_AppPool::getApp();
        }
        $url = "https://{$app->getHostName()}/app/creditCardSubmission/addCreditCard";
        return $url;
    }

    public static function requestCreditCardId($token, Mpp_Infusionsoft_App $app = null){

        $params = array(
            $token
        );

        $result = parent::send($app, "CreditCardSubmissionService.requestCreditCardId", $params);
        foreach ($result as $key => $value){
            $cc_data[ucfirst($key)] = $value;
        }
        $cc = new Mpp_Infusionsoft_CreditCard();
        $cc->loadFromArray($cc_data);
        return $cc;
    }

}