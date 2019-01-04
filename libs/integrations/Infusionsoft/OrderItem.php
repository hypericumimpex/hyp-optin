<?php
class Mpp_Infusionsoft_OrderItem extends Mpp_Infusionsoft_Generated_OrderItem{

    public $itemTypeMap = array(
        1 => 'Shipping',
        2 => 'Tax',
        3 => 'Service & Misc',
        4 => 'Product',
        5 => 'Upsell Product',
        6 => 'Finance Charge',
        7 => 'Special',
        8 => 'Program',
        9 => 'Subscription Plan',
        10 => 'Special: Free Trial Days',
        12 => 'Special: Order Total',
        13 => 'Special: Category',
        14 => 'Special: Shipping',
    );

    public function __construct($id = null, $app = null){
    	parent::__construct($id, $app);
    }

    public function __set($name, $value){
        if($name == 'ProductId'){
            $this->ItemType = 4;

        }
        parent::__set($name, $value);
    }

    public function save($app = null){

        if($this->Id == ''){
            $invoices = Mpp_Infusionsoft_DataService::query(new Mpp_Infusionsoft_Invoice(), array('JobId' => $this->OrderId), 1000, 0, false, $app);
            if(count($invoices) == 0){
                throw new Mpp_Infusionsoft_Exception("Could not get invoice for order: " . $this->OrderId . " while creating adding an order item.");
            }

            /** @var Mpp_Infusionsoft_Invoice $invoice */
            $invoice = array_shift($invoices);

            $result = Mpp_Infusionsoft_InvoiceService::addOrderItem($invoice->Id, $this->ProductId, $this->ItemType, $this->PPU, $this->Qty, $this->ItemDescription, $this->Notes, $app);
            if(!$result){
                throw new Mpp_Infusionsoft_Exception("Couldn't save orderitem: " . json_encode($this->toArray()));
            }

            //Infusionsoft doesn't always use consecutive Ids, but it is very very very rare that the id won't be consecutive if a new record is inserted very quickly after the previous, so even though this isn't ideal, it's what we are doing.
            $newestOrderItems = Mpp_Infusionsoft_DataService::queryWithOrderBy(new Mpp_Infusionsoft_OrderItem(), array('OrderId' => $this->OrderId), 'Id', false, 1);
            $newestOrderItem = array_shift($newestOrderItems);
            $this->Id = $newestOrderItem->Id;
        }
        $result = parent::save($app);

        return $result;
    }
}

