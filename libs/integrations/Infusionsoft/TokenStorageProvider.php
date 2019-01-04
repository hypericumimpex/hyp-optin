<?php
/**
 * Created by PhpStorm.
 * User: joey
 * Date: 4/29/2015
 * Time: 4:54 PM
 */

interface Mpp_Infusionsoft_TokenStorageProvider {
    public function saveTokens($appDomainName, $accessToken, $refreshToken, $expiresIn);
    public function getTokens($appDomainName);
}