<?php

class Mpp_Infusionsoft_WebFormService extends Mpp_Infusionsoft_WebFormServiceBase
{

    public static function ping($serviceName = 'WebFormService', Mpp_Infusionsoft_App $app = null)
    {
        parent::ping($serviceName, $app);
    }

    /**
     * Get a list of all web form names and IDs.
     *
     * @param Mpp_Infusionsoft_App $app
     * @return array List of web form names and IDs.
     */
    public static function getMap(Mpp_Infusionsoft_App $app = null)
    {
        $app = parent::getObjectOrDefaultAppIfNull($app);
        return $app->send("WebFormService.getMap", array());
    }

    /**
     * Get the web form's full HTML.
     *
     * Get the full HTML for the specified web form ID. This includes all HTML
     * tags and is meant to be used as a standalone webpage.
     *
     * @param int $webFormId The ID from Mpp_Infusionsoft_WebFormService::getMap.
     * @param Mpp_Infusionsoft_App $app
     * @return string Full HTML for the web form.
     */
    public static function getHTML($webFormId, Mpp_Infusionsoft_App $app = null)
    {
        $app = parent::getObjectOrDefaultAppIfNull($app);
        return $app->send("WebFormService.getHTML", array($webFormId));
    }

    /**
     * Get the web form's JavaScript snippet.
     *
     * Instead of having to use the HTML code, you can get the JavaScript
     * snippet. The web form displayed by the snippet will be updated
     * automatically when it is updated in Infusionsoft.
     *
     * @author Jacob Allred <jacob@novaksolutions.com>
     *
     * @param int $webFormId The ID from Mpp_Infusionsoft_WebFormService::getMap.
     * @param Mpp_Infusionsoft_App $app
     * @return string JavaScript snippet for the web form.
     */
    public static function getJavaScript($webFormId, Mpp_Infusionsoft_App $app = null)
    {
        $app = parent::getObjectOrDefaultAppIfNull($app);

        /*
         * The API doesn't provide a method of getting the JavaScript snippet.
         * Instead, we are going to get the HTML, find the form GUID, and create
         * the JavaScript snippet on our own.
         */

        // Get the HTML
        $html = Mpp_Infusionsoft_WebFormService::getHTML($webFormId, $app);

        // Create our search string
        $search = $app->getHostname() . '/app/form/process/';

        // Find the start and stop position of the form GUID
        $start = strpos($html, $search) + strlen($search);
        $stop = strpos(substr($html, $start), '"');

        // Pull out the GUID
        $guid = substr($html, $start, $stop);

        // Put together the JavaScript snippet
        $snippet = '<script type="text/javascript" src="https://';
        $snippet .= $app->getHostname();
        $snippet .= '/app/form/iframe/' . $guid . '"></script>';

        return $snippet;
    }

    /**
     * Get the web form's hosted URL.
     *
     * Instead of having to use the HTML code, you can get the URL to the
     * Infusionsoft hosted version of the web form.
     *
     * @author Jacob Allred <jacob@novaksolutions.com>
     *
     * @param int $webFormId The ID from Mpp_Infusionsoft_WebFormService::getMap.
     * @param Mpp_Infusionsoft_App $app
     * @return string URL of hosted web form
     */
    public static function getHostedURL($webFormId, Mpp_Infusionsoft_App $app = null)
    {
        $app = parent::getObjectOrDefaultAppIfNull($app);

        /*
         * The API doesn't provide a method of getting the hosted URL.
         * Instead, we are going to get the HTML, find the form GUID, and create
         * the hosted URL on our own.
         */

        // Get the HTML
        $html = Mpp_Infusionsoft_WebFormService::getHTML($webFormId, $app);

        // Create our search string
        return self::getHostedUrlFromHtml($html, $app);
    }

    /**
     * @param Mpp_Infusionsoft_App $app
     * @param $html
     * @return string
     */
    public static function getHostedUrlFromHtml($html, Mpp_Infusionsoft_App $app = null)
    {
        if($app == null){
            $app = parent::getObjectOrDefaultAppIfNull($app);
        }

        $search = $app->getHostname() . '/app/form/process/';

        // Find the start and stop position of the form GUID
        $start = strpos($html, $search) + strlen($search);
        $stop = strpos(substr($html, $start), '"');

        // Pull out the GUID
        $guid = substr($html, $start, $stop);

        // Put together the hosted URL
        $url = 'https://';
        $url .= $app->getHostname();
        $url .= '/app/form/' . $guid;

        return $url;
    }
}
