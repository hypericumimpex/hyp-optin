<?php
class Mpp_Infusionsoft_Payment extends Mpp_Infusionsoft_Generated_Payment{
    public function __construct($id = null, $app = null){
    	parent::__construct($id, $app);
    }


    public function __set($name, $value){
        if($name == 'OrderId'){
            $invoices = Mpp_Infusionsoft_DataService::query(new Mpp_Infusionsoft_Invoice(), array('JobId' => $value));
            $invoice = array_shift($invoices);
            $this->InvoiceId = $invoice->Id;
        } else {
            parent::__set($name, $value);
        }
    }

    public function save($app = null){
        if($this->Id == ''){
            $success = Mpp_Infusionsoft_InvoiceService::addManualPayment($this->InvoiceId, $this->PayAmt, $this->PayDate, $this->PayType, $this->PayNote, false, $app);
            if(!$success){
                throw new Mpp_Infusionsoft_Exception("Failed while saving payment: " . json_encode($this->toArray()));
            }
            $this->Id = 'Created, But, Cannot Get Id';
        }

        return true;
    }
}

