<?php
class Mpp_Infusionsoft_DataFormField extends Mpp_Infusionsoft_Generated_DataFormField{


    public function __construct($id = null, $app = null){
    	parent::__construct($id, $app);
    }

    public function getValues(){
        $out = array();
        if($this->canHaveCustomValues()){
            $out = explode("\n", $this->Values);
            $out = array_filter($out, "Mpp_Infusionsoft_DataFormField_isEmpty");
        } else {
            throw new Mpp_Infusionsoft_Exception($this->Label . ' cannot have values.');
        }
        return $out;
    }

    public function canHaveCustomValues()
    {
        return in_array($this->DataType, array(
            Mpp_Infusionsoft_CustomFieldService::$DataType_Dropdown,
            Mpp_Infusionsoft_CustomFieldService::$DataType_List,
            Mpp_Infusionsoft_CustomFieldService::$DataType_Radio
            )
        );
    }

    public function setValues(array $customFieldValues){
        if($this->DataType == Mpp_Infusionsoft_CustomFieldService::$DataType_Dropdown){
            $this->Values = "\n" . implode("\n", $customFieldValues);
        } else {
            throw new Mpp_Infusionsoft_Exception($this->Label . ' cannot have values.');
        }

    }

    public function addValue($value){
        $values = $this->getCustomFieldValues();
        $values[] = $value;
        $this->setCustomFieldValues($values);
    }

    public function removeValue($value){
        $success = false;

        $values = $this->getCustomFieldValues();
        $valueIndex = array_search($value, $values);
        if($valueIndex !== false){
            unset($value[$valueIndex]);
            $this->setCustomFieldValues($values);
            $success = true;
        }
        return $success;
    }
}

function Mpp_Infusionsoft_DataFormField_isEmpty($var){
    return !trim($var) == '';
}

