<?php

$infusionsoft_host = 'ydh434.infusionsoft.com';
$infusionsoft_api_key = 'dfgsdfgsdg54t456';

//To Add Custom Fields, use the addCustomField method like below.
//Mpp_Infusionsoft_Contact::addCustomField('_LeadScore');

//Below is just some magic...  Unless you are going to be communicating with more than one APP at the SAME TIME.  You can ignore it.
Mpp_Infusionsoft_AppPool::addApp(new Mpp_Infusionsoft_App($infusionsoft_host, $infusionsoft_api_key, 443));