<?php
class Mpp_Infusionsoft_Job extends Mpp_Infusionsoft_Generated_Job{
    var $customFieldFormId = -9;

    public function __construct($id = null, $app = null){
    	parent::__construct($id, $app);
    }

    public function addCustomFields($fields){
        foreach($fields as $name){
            self::addCustomField($name);
        }
	}

    public function save($app = null){
        if($this->Id == ''){
            $invoiceId = Mpp_Infusionsoft_InvoiceService::createBlankOrder($this->ContactId, $this->JobNotes, date('Ymd\TH:i:s', strtotime($this->StartDate)), 0, 0, $app);
            $invoice = new Mpp_Infusionsoft_Invoice($invoiceId);
            $this->Id = $invoice->JobId;
        }

        $result = parent::save($app);

        return $result;
    }
}
