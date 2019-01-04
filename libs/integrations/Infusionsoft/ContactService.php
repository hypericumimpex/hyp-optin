<?php
class Mpp_Infusionsoft_ContactService extends Mpp_Infusionsoft_ContactServiceBase{
    public static function getContactListWhoHaveATagInCategory($categoryName){
        $categories = Mpp_Infusionsoft_DataService::query(new Mpp_Infusionsoft_ContactGroupCategory(), array('CategoryName' => $categoryName));
        if(count($categories) > 0){
            $category = array_shift($categories);
        } else {
            throw new Exception("Tag Category: " . $categoryName . " doesn't exist.");
        }

        $tags = Mpp_Infusionsoft_DataService::query(new Mpp_Infusionsoft_ContactGroup(), array('GroupCategoryId' => $category->Id));
        $contactList = array();
        foreach($tags as $tag){
            /** @var Mpp_Infusionsoft_ContactGroup $tag */
            Mpp_Infusionsoft_ContactGroupAssign::addCustomField("Contact.FirstName");
            Mpp_Infusionsoft_ContactGroupAssign::addCustomField("Contact.LastName");
            $contacts = Mpp_Infusionsoft_DataService::query(new Mpp_Infusionsoft_ContactGroupAssign(), array('GroupId' => $tag->Id));
            foreach($contacts as $contact){
                /** @var Mpp_Infusionsoft_ContactGroupAssign $contact */
                if(!isset($contactList[$contact->ContactId])){
                    $contactList[$contact->ContactId] = $contact->__get('Contact.FirstName') . ' ' . $contact->__get('Contact.LastName');
                }
            }
        }

        return $contactList;
    }
}