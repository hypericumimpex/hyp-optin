<?php
/**
 * @property String GroupId
 * @property String ContactGroup
 * @property String DateCreated
 * @property String ContactId
 */
class Mpp_Infusionsoft_Generated_ContactGroupAssign extends Mpp_Infusionsoft_Generated_Base{
    protected static $tableFields = array('GroupId', 'ContactGroup', 'DateCreated', 'ContactId');


    public function __construct($id = null, $app = null){
    	parent::__construct('ContactGroupAssign', $id, $app);
    }

    public function getFields(){
		return self::$tableFields;
	}

	public function addCustomField($name){
		self::$tableFields[] = $name;
	}

    public function removeField($fieldName){
        $fieldIndex = array_search($fieldName, self::$tableFields);
        if($fieldIndex !== false){
            unset(self::$tableFields[$fieldIndex]);
            self::$tableFields = array_values(self::$tableFields);
        }
    }
}
