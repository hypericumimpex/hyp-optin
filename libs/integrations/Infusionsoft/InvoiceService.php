<?php
class Mpp_Infusionsoft_InvoiceService extends Mpp_Infusionsoft_InvoiceServiceBase{
    public static function chargeInvoiceArbitraryAmount($contactId, $invoiceId, $cardId, $amount, $merchantAccountId, $invoiceNotes = 'API Arbitrary Payment'){

    //Create a new order (InvoiceService.blankOrder...
        $dummyInvoiceId = Mpp_Infusionsoft_InvoiceService::createBlankOrder($contactId, $invoiceNotes . " Invoice: " . $amount, date('Ymd\TH:i:s'));

        try {
            //Add an order item that is the correct amount you want to charge...
            Mpp_Infusionsoft_InvoiceService::addOrderItem($dummyInvoiceId, 0, 3, $amount, 1, $invoiceNotes, "");
            //Set orders custom field "_ChargeStatus" to "Pending"
            $invoice = new Mpp_Infusionsoft_Invoice($dummyInvoiceId);
            $dummyOrder = new Mpp_Infusionsoft_Job($invoice->JobId);
            $dummyOrder->OrderStatus = "Pending";
            $dummyOrder->save();
            //Try to charge the invoice
            $result = Mpp_Infusionsoft_InvoiceService::chargeInvoice($dummyInvoiceId, $invoiceNotes, $cardId, $merchantAccountId, false);
        } catch(Exception $e) {
            Mpp_Infusionsoft_InvoiceService::deleteInvoice($dummyInvoiceId);
            throw new Exception("Failed to charge partial payment. Infusionsoft says: " . $e->getMessage());
        }

        if($result['Successful']) {
            //add a credit to the order
            Mpp_Infusionsoft_InvoiceService::addManualPayment($invoiceId, $amount, date('Ymd\TH:i:s'), "Credit Card", $invoiceNotes, false);
            $dummyOrder->OrderStatus = "Successful";
            $dummyOrder->save();

        } else {
            //erase the invoice
            Mpp_Infusionsoft_InvoiceService::deleteInvoice($dummyInvoiceId);

        }
        return $result;
}

    public static function chargeInvoiceArbitraryAmountUsingPayPlan($invoiceId, $cardId, $amount, $merchantAccountId, $paymentNotes = 'API Arbitrary Payment'){

        $result = false;

        //Get Invoice info so we can set the temporary PayPlan to match the amount we want to charge
        $invoice = new Mpp_Infusionsoft_Invoice($invoiceId);
        if ($amount + $invoice->TotalPaid <= $invoice->InvoiceTotal){
            $temporaryFirstPayment = $amount + $invoice->TotalPaid;
        } else {
            $temporaryFirstPayment = $invoice->InvoiceTotal - $invoice->TotalPaid;
        }

        //Get current PayPlan info so we can set it back after taking the payment
        $payPlan = Mpp_Infusionsoft_DataService::query(new Mpp_Infusionsoft_PayPlan(), array('InvoiceId' => $invoiceId));
        if (!empty($payPlan)){
            $payPlan = reset($payPlan);
            /**
             * @var Mpp_Infusionsoft_PayPlan $payPlan
             */

            $payPlanItems = Mpp_Infusionsoft_DataService::queryWithOrderBy(new Mpp_Infusionsoft_PayPlanItem(), array('PayPlanId' => $payPlan->Id), 'DateDue', true);

            $payPlanStartDate = $payPlan->StartDate;
            $payPlanFirstPaymentAmount = $payPlan->FirstPayAmt;
            $numberOfPayments = count($payPlanItems) - 1;
            $payPlanItemDueDate1 = null;
            $daysBetweenPayments = 1;
            foreach ($payPlanItems as $index => $payPlanItem){
                if ($index == 0){
                    continue;
                }
                /**
                 * @var Mpp_Infusionsoft_PayPlanItem $payPlanItem
                 */
                if ($payPlanItemDueDate1 == null){
                    $payPlanItemDueDate1 = $payPlanItem->DateDue;
                } else {
                    $daysBetweenPayments = round((strtotime($payPlanItem->DateDue) - strtotime($payPlanItemDueDate1)) / 60 / 60 / 24);
                    break;
                }
            }
            if ($payPlanItemDueDate1 == null){
                $payPlanItemDueDate1 = $payPlanStartDate;
            }
        } else { //If there is no PayPlan, then just set the order to the default of all due up front
            CakeLog::write('warning', 'PayPlan not found for InvoiceId: ' . $invoiceId . ' PayPlan will be set to the default');
            $payPlanFirstPaymentAmount = $invoice->InvoiceTotal;
            $payPlanStartDate = $invoice->DateCreated;
            $numberOfPayments = 0;
            $payPlanItemDueDate1 = $invoice->DateCreated;
            $daysBetweenPayments = 1;
        }
        try{
            Mpp_Infusionsoft_InvoiceService::addPaymentPlan($invoiceId, 0, $cardId, $merchantAccountId, 1, 1, $temporaryFirstPayment, Mpp_Infusionsoft_App::formatDate(date('Y-m-d')), Mpp_Infusionsoft_App::formatDate(date('Y-m-d', strtotime(' + 1 day'))), 1, 1);

            $result = Mpp_Infusionsoft_InvoiceService::chargeInvoice($invoiceId, $paymentNotes, $cardId, $merchantAccountId, false);
        } catch (Exception $e){
            CakeLog::write('error', 'Failed to charge invoice arbitrary amount! InvoiceId: ' . $invoiceId . ' Infusionsoft error: ' . $e->getMessage());
        }

        try{
            Mpp_Infusionsoft_InvoiceService::addPaymentPlan($invoiceId, 0, $cardId, $merchantAccountId, 1, 3, $payPlanFirstPaymentAmount, $payPlanStartDate, $payPlanItemDueDate1, $numberOfPayments, $daysBetweenPayments);
        } catch (Exception $e){
            CakeLog::write('error', 'Failed to reset payment plan after chargeInvoiceArbitraryAmount! InvoiceId: ' . $invoiceId . ' PayPlan Details: ' . json_encode(compact('payPlanFirstPaymentAmount', 'payPlanStartDate', 'payPlanItemDueDate1', 'numberOfPayments', 'daysBetweenPayments')));
        }

        return $result;
    }

}