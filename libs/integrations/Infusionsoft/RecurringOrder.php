<?php
class Mpp_Infusionsoft_RecurringOrder extends Mpp_Infusionsoft_Generated_RecurringOrder{
    public $customFieldFormId = -10;
    public $freeTrialDays = 0;

    public static $billingCycleMap = array(
        'month' => 2,
        'day' => 6,
        'week' => 3,
    );
    public function __construct($id = null, $app = null){
    	parent::__construct($id, $app);
    }

    //Find the Id first order charged for this subscription
    public static function getFirstOrderId ($recurringOrderId) {
        //load recurringOrder
        $recurringOrder = new Mpp_Infusionsoft_RecurringOrder($recurringOrderId);

        //If there was an originating shopping cart or order form order, that is the first order
        if ($recurringOrder->OriginatingOrderId != 0) {
            return $recurringOrder->OriginatingOrderId;
        } else {
            //find all Orders with a matching JobRecurringId and put them in this array, sorted by date.
            $matchingOrders = Mpp_Infusionsoft_DataService::queryWithOrderBy(new Mpp_Infusionsoft_Job(), array('JobRecurringId' => $recurringOrderId),'DateCreated');

            if (!empty($matchingOrders)){
                $earliestMatchingOrder = array_shift($matchingOrders);
                return $earliestMatchingOrder->Id;
            } else {
                return false;
            }

        }
    }

    public static function getLastOrderId ($recurringOrderId) {
        //find all Orders with a matching JobRecurringId and put them in this array, sorted by date.
        $matchingOrders = Mpp_Infusionsoft_DataService::queryWithOrderBy(new Mpp_Infusionsoft_Job(), array('JobRecurringId' => $recurringOrderId),'DateCreated', false);

        if (empty($matchingOrders)){
            $subscription = new Mpp_Infusionsoft_RecurringOrder($recurringOrderId);
            if ($subscription->OriginatingOrderId != null){
                $matchingOrders[] = new Mpp_Infusionsoft_Job($subscription->OriginatingOrderId);
            }
        }
        if (!empty($matchingOrders)){
            $latestMatchingOrder = array_shift($matchingOrders);
            return $latestMatchingOrder->Id;
        } else {
            return false;
        }
    }

    public static function getSubscriptionFromOrder($orderId){
        try{
            $order = new Mpp_Infusionsoft_Job($orderId);
            if (!empty($order->JobRecurringId)){
                return new Mpp_Infusionsoft_RecurringOrder($order->JobRecurringId);
            } else {
                $subscription = Mpp_Infusionsoft_DataService::query(new Mpp_Infusionsoft_RecurringOrder(), array('OriginatingOrderId' => $orderId));
                if (!empty($subscription)){
                    return $subscription[0];
                } else {
                    return false;
                }
            }
        } catch (Exception $e){
            CakeLog::write('error', 'getSusbscriptionIdForOrder failed to get the Order! orderId: ' . $orderId);
            return false;
        }
    }

    public function __set($name, $value)
    {
        if(in_array($name, array('Frequency', 'BillingCycle'))) {
            $value = (int) $value;
        }

        parent::__set($name, $value);
    }

    public function save($app = null){
        if($this->Id == ''){
            $id = Mpp_Infusionsoft_InvoiceService::addRecurringOrder($this->ContactId, true, $this->SubscriptionPlanId, $this->Qty, $this->BillingAmt, true, $this->MerchantAccountId, $this->CC1, $this->AffiliateId, $this->freeTrialDays);
            $this->Id = $id;
        }

        $result = parent::save($app);

        if ($this->NextBillDate != null){
            Mpp_Infusionsoft_InvoiceService::updateJobRecurringNextBillDate($this->Id, $this->NextBillDate);
        }

        //Mpp_Infusionsoft_InvoiceService::createInvoiceForRecurring($this->Id);
        return $result;
    }
}