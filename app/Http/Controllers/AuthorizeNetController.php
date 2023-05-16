<?php

namespace App\Http\Controllers;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Illuminate\Http\Request;

class AuthorizeNetController extends Controller
{

    public function store(Request $request)
    {
        $amount = $request->input('amount');
        /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
       $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
       $merchantAuthentication->setName('5yw48T8AM');
       $merchantAuthentication->setTransactionKey('7g5S62Zew26VeKJP');
       
       // Set the transaction's refId
       $refId = 'ref' . time();
        echo '20'.$request->input('card_year').'-'.$request->input('card_month');
       // Create the payment data for a credit card
       $creditCard = new AnetAPI\CreditCardType();
       $creditCard->setCardNumber($request->input('card_number'));
       $creditCard->setExpirationDate('20'.$request->input('card_year').'-'.$request->input('card_month'));
       $creditCard->setCardCode($request->input('card_cvv'));
   
       // Add the payment data to a paymentType object
       $paymentOne = new AnetAPI\PaymentType();
       $paymentOne->setCreditCard($creditCard);
   
       // Create order information
       $order = new AnetAPI\OrderType();
       $order->setInvoiceNumber($request->input('invoice_id'));
       $order->setDescription("Payment via Card Swipe in POS");
   
       // Set the customer's Bill To address
       $customerAddress = new AnetAPI\CustomerAddressType();
       $customerAddress->setFirstName($request->input('card_first_name'));
       $customerAddress->setLastName($request->input('card_last_name'));
       $customerAddress->setCountry("USA");
   
       // Create a TransactionRequestType object and add the previous objects to it
       $transactionRequestType = new AnetAPI\TransactionRequestType();
       $transactionRequestType->setTransactionType("authOnlyTransaction"); 
       $transactionRequestType->setAmount($amount);
       $transactionRequestType->setOrder($order);
       $transactionRequestType->setPayment($paymentOne);
       $transactionRequestType->setBillTo($customerAddress);
   

       // Assemble the complete transaction request
       $request = new AnetAPI\CreateTransactionRequest();
       $request->setMerchantAuthentication($merchantAuthentication);
       $request->setRefId($refId);
       $request->setTransactionRequest($transactionRequestType);
   
       // Create the controller and get the response
       $controller = new AnetController\CreateTransactionController($request);
       $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
   
   
       if ($response != null) {
           // Check to see if the API request was successfully received and acted upon
           if ($response->getMessages()->getResultCode() == "Ok") {
               // Since the API request was successful, look for a transaction response
               // and parse it to display the results of authorizing the card
               $tresponse = $response->getTransactionResponse();
           
               if ($tresponse != null && $tresponse->getMessages() != null) {
                   echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
                   echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
                   echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
                   echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
                   echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";
               } else {
                   echo "Transaction Failed \n";
                   if ($tresponse->getErrors() != null) {
                       echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                       echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
                   }
               }
               // Or, print errors if the API request wasn't successful
           } else {
               echo "Transaction Failed \n";
               $tresponse = $response->getTransactionResponse();
           
               if ($tresponse != null && $tresponse->getErrors() != null) {
                   echo " Error Code  : " . $tresponse->getErrors()[0]->getErrorCode() . "\n";
                   echo " Error Message : " . $tresponse->getErrors()[0]->getErrorText() . "\n";
               } else {
                   echo " Error Code  : " . $response->getMessages()->getMessage()[0]->getCode() . "\n";
                   echo " Error Message : " . $response->getMessages()->getMessage()[0]->getText() . "\n";
               }
           }      
       } else {
           echo  "No response returned \n";
       }
   
       return $response;


    }

}
