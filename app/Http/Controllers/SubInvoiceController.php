<?php

namespace App\Http\Controllers;

use App\AC_Mediator;
use Illuminate\Http\Request;
use App\Subinvoice;

use App\User;
use App\Shipping_address;
use App\Csr_note;
use App\UserSubscription;
use App\Product;
use App\Referral;
use App\Order;
use DateTime;
use Date;
use App\Shippingholds;
use DateTimeZone;
use DB;

class SubinvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Show the application dashboard.
     *
     * @return Response
     */
    public function show()
    {
        
    }



	public function TestDate() {
		
			//figure out date logic for trial period - 
			// - mist be UNIX timestamp
			
			$trial_ends = "";
			
			//time of day cutoff for orders
			$cutOffTime = "15:00:00";
			$cutOffDay = "Wednesday";
			
			//change dates to WEDNESDAY
			//cutoff date is the last date to change or to signup for THIS week
			$cutOffDate = new DateTime();
			$cutOffDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
			$cutOffDate->modify('this ' . $cutOffDay . ' ' . $cutOffTime);
		
			//get today's date
			$todaysDate = new DateTime();
			$todaysDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
			$currentDay = date_format($todaysDate, "l");
			$currentTime = date_format($todaysDate, "H:is");
			
			echo "Today is " . $currentDay . "<br />";
			
			echo "Cut off date: " . $cutOffDate->format('Y-m-d H:i:s') . "<br />";
			echo "Current time: " . $todaysDate->format('Y-m-d H:i:s') . "<br />";
			
			//check to see if today is the same day as the cutoff day
			if ($currentDay==$cutOffDay) {
				
				//check to see if it's BEFORE the cutoff tine. If so, then this is a special case
				if ($currentTime < $cutOffTime) {

					//ok, so it's the day of the cutoff, but before time has expired
					//SET the trial_ends date to $cutOffDate - no problem
					echo "You have JUST beat the cutoff period <br /> Setting the trial_ends to today"; 
					$trial_ends = $cutOffDate;

				}else{

					//the cutoff tiem has just ended
					//now, set the date to NEXT $cutOffDate
					$trial_ends = new DateTime();
					$trial_ends->setTimeZone(new DateTimeZone('America/Los_Angeles'));
					$trial_ends->modify('next ' . $cutOffDay . ' ' . $cutOffTime);
					echo "You have missed the cutoff period <br /> Setting the trial_ends to next week"; 
					

				}
			
			}else{
				
				//today is not the same as the trial ends date, so simply set the date to the next cutoff 
				$trial_ends = $cutOffDate;
				
			}
		
			echo "Trial Ends: " . $trial_ends->format('Y-m-d H:i:s')  . "<br />";
			
			echo "UNIX version of timestamp: " . $trial_ends->getTimestamp() . "<br />";
		
			$TestDate = new DateTime('@1470463200');
			$TestDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
			echo "Converted back:" . $TestDate->format('Y-m-d H:i:s') . "<br />";
			
	}
	
	public function getMenuTitles ($user_id) {
		
		$DeliveryDay = "Tuesday";
		$Menu_string = "";

			//change dates to WEDNESDAY
			//cutoff date is the last date to change or to signup for THIS week
			$DeliveryDate = new DateTime();
			$DeliveryDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
			$DeliveryDate->modify('this ' . $DeliveryDay);

			$DeliveryDate_string = $DeliveryDate->format('Y-m-d');

			$MenuUsers = DB::table('menus_users')
					->where('users_id', $user_id)
					->where('delivery_date',$DeliveryDate_string)
					->get();

			foreach ($MenuUsers as $MenuUser) {

			//	echo ($MenuUser->menus_id . "<br />");
				$Menu = DB::table('menus')->where('id', $MenuUser->menus_id)->first();
				$Menu_string .= $Menu->menu_title . ", ";

			}
		
			return ($Menu_string);
		
	}

	public function getOrderXML() {
		
			/****
			UPDATE query to only select invoices who fall in the correct date range

			ADD pagination feature for large volumes of orders

			****/
			
			
		//get date ranges
		//get today's date
		$todaysDate = new DateTime();
		$todaysDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
		$todaysDate_format = date_format($todaysDate, "Y-m-d H:i:s");
		
		
		$ship_xml = '<?xml version="1.0" encoding="utf-8"?><Orders>';
		
		//retrieve all orders with status of "charged_not_shipped" from invoice table
		$subinvoices = Subinvoice::where('invoice_status','charged_not_shipped')
							->where('period_start_date', '<', $todaysDate_format)
							->where('period_end_date', '>', $todaysDate_format)
							->get();
		
		//loop over the invoices to create Order records
		
		foreach ($subinvoices as $invoice) {
			
			//if this is a 0 dollar invoice and doesn't have coupon ForeverFreeX8197 OR has an amount > 0 
			
			$amountCharged = $invoice->charge_amount;
			
		
																								
			if ( ($amountCharged != '0') || ($amountCharged=='0' && $invoice->coupon_code=='ForeverFreeX8197') ) {
			
			
				//use the user_id field to get the User and Current Shipping Address from user, usersubscriptions, and shipping_addresses
				$id = $invoice->user_id;
				$stripe_id = $invoice->stripe_sub_id;			

				$user = User::where('id', $id)->first();
			
				if ($user) {	
					$shippingAddress = Shipping_address::where('user_id',$id)->where('is_current', '1')->first();
				}
			
			
				//figure out the user's preferences - 
			
				//figure out this week's menus - 
			
				//check to see if there's a shipping address - if not, create an order exception
			
				if ($shippingAddress) {
					//create a new record in the Orders table
					$order = new Order;
					$order->save();

					//get the OrderID from the order table
					//add arbitrary number to the primary key to obfuscate order ids

					$order_id = $order->id + 11565;
					$order->order_id = $order_id;
					$order->save();	
			
					//add a batch ID so that we can easily get all the orders sent to ship station
			
					$charge_date = new DateTime($invoice->charge_date);
					$charge_date_formatted = $charge_date->format('m/d/Y H:i:s');	
				
					$subscriber = UserSubscription::where('stripe_id',$stripe_id)->first();
				
					if ($subscriber) {
							$product = Product::where('id',$subscriber->product_id)->first();
				
							$ship_xml .= "<Order>";

							$ship_xml .= "<OrderID><![CDATA[" . $order_id . "]]></OrderID>";
							$ship_xml .= "<OrderNumber><![CDATA[" . $order_id . "]]></OrderNumber>";
							$ship_xml .= "<OrderDate>" . $charge_date_formatted . "</OrderDate>";
							$ship_xml .= "<OrderStatus><![CDATA[paid]]></OrderStatus>";
							$ship_xml .= "<LastModified>" . $charge_date_formatted . "</LastModified>";
							$ship_xml .= "<ShippingMethod><![CDATA[OnTrac]]></ShippingMethod>";
							$ship_xml .= "<PaymentMethod><![CDATA[Credit Card]]></PaymentMethod>";
							$ship_xml .= "<OrderTotal>" . $invoice->charge_amount / 100 . "</OrderTotal>";
							$ship_xml .= "<TaxAmount>0.00</TaxAmount>";
							$ship_xml .= "<ShippingAmount>0.00</ShippingAmount>";
							$ship_xml .= "<CustomerNotes><![CDATA[Add Note from User field!]]></CustomerNotes>";
							$ship_xml .= "<InternalNotes><![CDATA[]]></InternalNotes>";
							$ship_xml .= "<Gift>false</Gift>";
				
				
							//GET menu titles for this user
							$menu_titles = $this->getMenuTitles($id);
							
							if (isset($subscriber->dietary_preferences)) {
								$dietary_prefs_string = $subscriber->getDietaryPreferencesAttribute($subscriber->dietary_preferences);
							} else {
								$dietary_prefs_string = "";
							}
							
							$ship_xml .= "<CustomField1><![CDATA[" . $menu_titles . "]]></CustomField1>";
							$ship_xml .= "<CustomField2><![CDATA[" . $dietary_prefs_string . "]]></CustomField2>";
							$ship_xml .= "<CustomField3><![CDATA[" . $shippingAddress->delivery_instructions . "]]></CustomField3>";
			
			
			
							//create the customer data
							$ship_xml .= "<Customer>";
							$ship_xml .= "";
			
							$ship_xml .= "<CustomerCode><![CDATA[" . $user->email . "]]></CustomerCode>";
							$ship_xml .= "<BillTo>";
							$ship_xml .= "<Name><![CDATA[" .$user->name . "]]></Name>";
							$ship_xml .= "<Company></Company>";
							$ship_xml .= "<Phone><![CDATA[" . $user->phone . "]]></Phone>";
							$ship_xml .= "<Email><![CDATA[" . $user->email . "]]></Email>";
							$ship_xml .= "</BillTo>";
			
							$ship_xml .= "<ShipTo>";
							$ship_xml .= "<Name><![CDATA[" . $shippingAddress->shipping_first_name . " " . $shippingAddress->shipping_last_name . "]]></Name>";
							$ship_xml .= "<Company><![CDATA[]]></Company>";
							$ship_xml .= "<Address1><![CDATA[" . $shippingAddress->shipping_address . "]]></Address1>";
							$ship_xml .= "<Address2>" . $shippingAddress->shipping_address_2 . "</Address2>";
							$ship_xml .= "<City><![CDATA[" . $shippingAddress->shipping_city . "]]></City>";
							$ship_xml .= "<State><![CDATA[" . $shippingAddress->shipping_state . "]]></State>";
							$ship_xml .= "<PostalCode><![CDATA[" . $shippingAddress->shipping_zip . "]]></PostalCode>";
							$ship_xml .= "<Country><![CDATA[US]]></Country>";
							$ship_xml .= "<Phone><![CDATA[" . $shippingAddress->phone1 . "]]></Phone>";
							$ship_xml .= "</ShipTo>";

							$ship_xml .= "</Customer>";



							$ship_xml .= "<Items><Item>";

							$ship_xml .= "<SKU><![CDATA[" . $product->sku ."]]></SKU>";
							$ship_xml .= "<Name><![CDATA[" . $product->product_description . "]]></Name>";
							$ship_xml .= "<ImageUrl></ImageUrl>";
							$ship_xml .= "<Weight>0</Weight>";
							$ship_xml .= "<WeightUnits></WeightUnits>";
							$ship_xml .= "<Quantity>1</Quantity>";
							$ship_xml .= "<UnitPrice>" . $product->cost . "</UnitPrice>";
							$ship_xml .= "<Location></Location>";

							$ship_xml .= "<Options>";

							$ship_xml .= "<Option>";

							$ship_xml .= "<Name><![CDATA[]]></Name>";
							$ship_xml .= "<Value><![CDATA[]]></Value>";
							$ship_xml .= "<Weight>0</Weight>";

							$ship_xml .= "</Option>";

							$ship_xml .= "<Option>";

							$ship_xml .= "<Name><![CDATA[]]></Name>";
							$ship_xml .= "<Value></Value>";
							$ship_xml .= "<Weight>0</Weight>";

							$ship_xml .= "</Option>";

							$ship_xml .= "</Options>";

							$ship_xml .= "</Item></Items>";

							$invoice->invoice_status = "sent_to_ship";
							$invoice->order_id = $order_id;
							$invoice->save();


							$ship_xml .= "</Order>";
						}
					}
				
					}else{
						//update the status
						$invoice->invoice_status = "does_not_ship";
						$invoice->save();
						
					}//end if
				} //end foreach
		
		$ship_xml .= '</Orders>';
			
		//update invoices table
		//update orders table
	
		//record XML in log table
		
		
		//echo XML
		return $ship_xml;
		
		
	}
	
	public function testMenus() {
		
		//user 
		
		//use "this Tuesday" since we'll always be looking ahead
		
		$DeliveryDay = "Tuesday";
		$Menu_string = "";
			
		//change dates to WEDNESDAY
		//cutoff date is the last date to change or to signup for THIS week
		$DeliveryDate = new DateTime();
		$DeliveryDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
		$DeliveryDate->modify('this ' . $DeliveryDay);
		
		$DeliveryDate_string = $DeliveryDate->format('Y-m-d');
		
		$MenuUsers = DB::table('menus_users')
				->where('users_id', '2279')
				->where('delivery_date',$DeliveryDate_string)
				->get();
		
		foreach ($MenuUsers as $MenuUser) {
		
		//	echo ($MenuUser->menus_id . "<br />");
			$Menu = DB::table('menus')->where('id', $MenuUser->menus_id)->first();
			$Menu_string .= $Menu->menu_title . ", ";
		
		}
		echo ($Menu_string);
	}
	
	public function updateShippingStatus(Request $request) {
		
		$shipXML = @file_get_contents("php://input");
		
		
		$shipnotice = simplexml_load_string($shipXML);
		$order_id = $shipnotice->OrderID;
		
		$order = Order::where('order_id',$order_id)->first();
		
		//convert the date
		$order->ship_date = $shipnotice->ShipDate;
		$order->ship_carrier = $shipnotice->Carrier;
		$order->ship_service = $shipnotice->Service;
		$order->tracking_number = $shipnotice->TrackingNumber;
		$order->ship_station_xml = $shipXML;
		$order->save();
		
		//mark subinvoice as "shipped"
		$invoice = Subinvoice::where('order_id',$order_id)->first();
		$invoice->invoice_status = "shipped";
		$invoice->save();
		
		
		http_response_code(200); // PHP 5.4 or greater
	}
	
	public function testShippingStatus () {
		
	
		$shipXML = '<?xml version="1.0" encoding="utf-8"?>

		<ShipNotice>

		  <OrderNumber>123456</OrderNumber>

		  <OrderID>123456</OrderID>

		  <CustomerCode>customer@mystore.com</CustomerCode>

		  <LabelCreateDate>12/8/2011 12:56 PM</LabelCreateDate>

		  <ShipDate>12/8/2011</ShipDate>

		  <Carrier>OnTrac</Carrier>

		  <Service></Service>

		  <TrackingNumber>1Z909084330298430820</TrackingNumber>

		  <ShippingCost>4.95</ShippingCost>

		  <Recipient>

		    <Name>The President</Name>

		    <Company>US Govt</Company>

		    <Address1>1600 Pennsylvania Ave</Address1>

		    <Address2></Address2>

		    <City>Washington</City>

		    <State>DC</State>

		    <PostalCode>20500</PostalCode>

		    <Country>US</Country>

		  </Recipient>

		  <Items>

		    <Item>

		      <SKU>FD88821</SKU>

		      <Name>My Product Name</Name>

		      <Quantity>2</Quantity>

		    </Item>

		  </Items>

		</ShipNotice>';
		
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,            "http://onepotato.app/shipstation/getorders" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $shipXML);
		
			curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

			$result=curl_exec ($ch);

			print_r($result);
		
	}
	
	public function recordStripeInvoice() {
		
			// Set your secret key: remember to change this to your live secret key in production
			// See your keys here https://dashboard.stripe.com/account/apikeys
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

			// Retrieve the request's body and parse it as JSON
			$input = @file_get_contents("php://input");
			$event_json = json_decode($input);
//file_put_contents(__DIR__."/../../../storage/logs/stripe.log", $input);
		// Do something with $event_json

		//record the invoice data to the database


		//record the RAW JSON to the log (need to create)

		//record the parsed JSON to the database


        switch($event_json->type) {
            case 'invoice.payment_failed':
                $this->_invoicePaymentFailed($event_json);
                break;

            case 'invoice.payment_succeeded':
            default:
                $this->_invoicePaymentSucceeded($event_json);
                break;
        }

		http_response_code(200); // PHP 5.4 or greater
		
		
	}

    private function _invoicePaymentFailed($event_json) {
        // Need to send data to Active Campaign
        // By adding tag 'CC-Fail' to the correct customer
        // We are able to get him by subscription_id from stripe
        // also need to do something with "Payment Fail Count" -- looks like we need to check this and increment, may be field will be added to DB later, but can't find it now
        $user = $this->_getUser($event_json);

        if($user instanceof User) {
            $ac = AC_Mediator::GetInstance();
            $ac->PaymentFailed($user);
        }
    }

    private function _getUser($event_json) {
        $stripeCustomerId = $event_json->data->object->customer;
        $stripeSubscriptionId = $event_json->data->object->lines->data[0]->id;

        $user = User::where('stripe_id', $stripeCustomerId)->first();

        if(!$user) {
            $subscription = UserSubscription::where('stripe_id', $stripeSubscriptionId)->first();
            $userId = $subscription->user_id;
            $user = User::find($userId);
        }

        return $user;
    }

    private function _invoicePaymentSucceeded($event_json) {
        //date function here is pesky
        $subinvoice = new Subinvoice;

        $subinvoice->stripe_event_id = $event_json->id;

        $timeStamp = $event_json->data->object->date;
        $dtStr = date("c", $timeStamp);
        $charge_date = new DateTime($dtStr);

        //$charge_date = new DateTime();
        //$charge_date_formatted = $charge_date->format("Y-m-d H:i:s");

        //store trialing days

        //store period_start_date, period_end_date

        $charge_date_formatted = date_format($charge_date,"Y-m-d H:i:s");


        $subinvoice->charge_date = $charge_date_formatted;
        $subinvoice->stripe_customer_id = $event_json->data->object->customer;

        if (isset($event_json->data->object->charge)) {
            $subinvoice->stripe_charge_code = $event_json->data->object->charge;
        }

        $subinvoice->stripe_sub_id = $event_json->data->object->lines->data[0]->id;

        $period_start_date_unix = $event_json->data->object->lines->data[0]->period->start;
        $period_end_date_unix = $event_json->data->object->lines->data[0]->period->end;

        $period_start_date_str = date("c", $period_start_date_unix);
        $period_start_date = new DateTime($period_start_date_str);
        $period_start_date_formatted = date_format($period_start_date,"Y-m-d H:i:s");

        $period_end_date_str = date("c", $period_end_date_unix);
        $period_end_date = new DateTime($period_end_date_str);
        $period_end_date_formatted = date_format($period_end_date,"Y-m-d H:i:s");

        $subinvoice->period_start_date = $period_start_date_formatted;
        $subinvoice->period_end_date = $period_end_date_formatted;

        $stripe_id = $event_json->data->object->lines->data[0]->id;

        //coupon code (may not exist)
        if (isset($event_json->data->object->discount->coupon->id)) {
            $subinvoice->coupon_code = $event_json->data->object->discount->coupon->id;
        }


        $subinvoice->charge_amount = $event_json->data->object->lines->data[0]->amount;
        $subinvoice->plan_id = $event_json->data->object->lines->data[0]->plan->id;
        $subinvoice->invoice_status = "charged_not_shipped";
        $subinvoice->raw_json = json_encode($event_json);


        //link user_id

        $subscriber = UserSubscription::where('stripe_id',$stripe_id)->first();
        $subinvoice->user_id = $subscriber->user_id;

        $subinvoice->save();
    }


/*
{
  "created": 1326853478,
  "livemode": false,
  "id": "evt_00000000000000",
  "type": "invoice.payment_failed",
  "object": "event",
  "request": null,
  "pending_webhooks": 1,
  "api_version": "2016-07-06",
  "data": {
    "object": {
      "id": "in_00000000000000",
      "object": "invoice",
      "amount_due": 8544,
      "application_fee": null,
      "attempt_count": 1,
      "attempted": true,
      "charge": "ch_00000000000000",
      "closed": false,
      "currency": "usd",
      "customer": "cus_00000000000000",
      "date": 1472684478,
      "description": null,
      "discount": null,
      "ending_balance": 0,
      "forgiven": false,
      "lines": {
        "data": [
          {
            "id": "sub_96qcU08gQQ8NRL",
            "object": "line_item",
            "amount": 8544,
            "currency": "usd",
            "description": null,
            "discountable": true,
            "livemode": true,
            "metadata": {
            },
            "period": {
              "start": 1473289200,
              "end": 1473894000
            },
            "plan": {
              "id": "omn_2_adults_4_child_gf_1",
              "object": "plan",
              "amount": 13494,
              "created": 1472049058,
              "currency": "usd",
              "interval": "week",
              "interval_count": 1,
              "livemode": false,
              "metadata": {
              },
              "name": "Omnivore Box for 2 Adults and 4 Children Gluten Free",
              "statement_descriptor": null,
              "trial_period_days": null
            },
            "proration": false,
            "quantity": 1,
            "subscription": null,
            "type": "subscription"
          }
        ],
        "total_count": 1,
        "object": "list",
        "url": "/v1/invoices/in_18ohhqLxTAk76ScVHotpKDiV/lines"
      },
      "livemode": false,
      "metadata": {
      },
      "next_payment_attempt": null,
      "paid": false,
      "period_end": 1472684400,
      "period_start": 1472532264,
      "receipt_number": null,
      "starting_balance": 0,
      "statement_descriptor": null,
      "subscription": "sub_00000000000000",
      "subtotal": 8544,
      "tax": null,
      "tax_percent": null,
      "total": 8544,
      "webhooks_delivered_at": 1472684478
    }
  }
}
	 */


/*
{
  "created": 1326853478,
  "livemode": false,
  "id": "evt_00000000000000",
  "type": "invoice.payment_succeeded",
  "object": "event",
  "request": null,
  "pending_webhooks": 1,
  "api_version": "2016-07-06",
  "data": {
    "object": {
      "id": "in_00000000000000",
      "object": "invoice",
      "amount_due": 8544,
      "application_fee": null,
      "attempt_count": 1,
      "attempted": true,
      "charge": "_00000000000000",
      "closed": true,
      "currency": "usd",
      "customer": "cus_00000000000000",
      "date": 1472684478,
      "description": null,
      "discount": null,
      "ending_balance": 0,
      "forgiven": false,
      "lines": {
        "data": [
          {
            "id": "sub_96qcU08gQQ8NRL",
            "object": "line_item",
            "amount": 8544,
            "currency": "usd",
            "description": null,
            "discountable": true,
            "livemode": true,
            "metadata": {
            },
            "period": {
              "start": 1473289200,
              "end": 1473894000
            },
            "plan": {
              "id": "omn_2_adults_4_child_gf_1",
              "object": "plan",
              "amount": 13494,
              "created": 1472049058,
              "currency": "usd",
              "interval": "week",
              "interval_count": 1,
              "livemode": false,
              "metadata": {
              },
              "name": "Omnivore Box for 2 Adults and 4 Children Gluten Free",
              "statement_descriptor": null,
              "trial_period_days": null
            },
            "proration": false,
            "quantity": 1,
            "subscription": null,
            "type": "subscription"
          }
        ],
        "total_count": 1,
        "object": "list",
        "url": "/v1/invoices/in_18ohhqLxTAk76ScVHotpKDiV/lines"
      },
      "livemode": false,
      "metadata": {
      },
      "next_payment_attempt": null,
      "paid": true,
      "period_end": 1472684400,
      "period_start": 1472532264,
      "receipt_number": null,
      "starting_balance": 0,
      "statement_descriptor": null,
      "subscription": "sub_00000000000000",
      "subtotal": 8544,
      "tax": null,
      "tax_percent": null,
      "total": 8544,
      "webhooks_delivered_at": 1472684478
    }
  }
}
 */
	public function testStripeJSON() {
		
		$input = '{
		  "created": 1326853478,
		  "livemode": false,
		  "id": "evt_00000000000000",
		  "type": "invoice.payment_succeeded",
		  "object": "event",
		  "request": null,
		  "pending_webhooks": 1,
		  "api_version": "2016-03-07",
		  "data": {
		    "object": {
		      "id": "in_00000000000000",
		      "object": "invoice",
		      "amount_due": 1200,
		      "application_fee": null,
		      "attempt_count": 1,
		      "attempted": true,
		      "charge": "_00000000000000",
		      "closed": true,
		      "currency": "usd",
		      "customer": "cus_00000000000000",
		      "date": 1467583569,
		      "description": null,
		      "discount": null,
		      "ending_balance": 0,
		      "forgiven": false,
		      "lines": {
		        "data": [
		          {
		            "id": "sub_8iBeLD4bRSABxM",
		            "object": "line_item",
		            "amount": 1498,
		            "currency": "usd",
		            "description": null,
		            "discountable": true,
		            "livemode": true,
		            "metadata": {
		            },
		            "period": {
		              "start": 1468187931,
		              "end": 1468792731
		            },
		            "plan": {
		              "id": "test_plan_1",
		              "object": "plan",
		              "amount": 1000,
		              "created": 1466701834,
		              "currency": "usd",
		              "interval": "week",
		              "interval_count": 1,
		              "livemode": false,
		              "metadata": {
		              },
		              "name": "Test Plan 1",
		              "statement_descriptor": null,
		              "trial_period_days": null
		            },
		            "proration": false,
		            "quantity": 1,
		            "subscription": null,
		            "type": "subscription"
		          }
		        ],
		        "total_count": 1,
		        "object": "list",
		        "url": "/v1/invoices/in_18TIj7CEzFD8tUOqQjZFX76H/lines"
		      },
		      "livemode": false,
		      "metadata": {
		      },
		      "next_payment_attempt": null,
		      "paid": true,
		      "period_end": 1467583131,
		      "period_start": 1466978331,
		      "receipt_number": null,
		      "starting_balance": 0,
		      "statement_descriptor": null,
		      "subscription": "sub_00000000000000",
		      "subtotal": 1200,
		      "tax": null,
		      "tax_percent": null,
		      "total": 1200,
		      "webhooks_delivered_at": 1467583569
		    }
		  }
		}';
		
		$event_json = json_decode($input);
		echo "Created: " . $event_json->created;
		echo "<br />";
		echo "LiveMode: " . $event_json->livemode;
		echo "<br />";
		echo "**ID: " . $event_json->id;
		echo "<br />";
		echo "type: " . $event_json->type;
		echo "<br />";
		echo "object: " . $event_json->object;
		echo "<br />";
		echo "request: " . $event_json->request;
		echo "<br />";
		echo ("** data->object->date: " . date("l M j, Y",$event_json->data->object->date));
		echo "<br />";
		echo ("** data->object->id: " .  $event_json->data->object->id);
		echo "<br />";
		echo ("** data->object->customer: " .  $event_json->data->object->customer);
		echo "<br />";
		echo ("** data->object->date: " .  date("Y-m-d H:i:s", $event_json->data->object->date));
		echo "<br />";
		echo ("data->object->period_start: " .  $event_json->data->object->period_start);
		echo "<br />";
		echo ("data->object->period_end: " .  $event_json->data->object->period_start);
		echo "<br />";
		echo ("** data->object->lines->data[0]->id: " .  $event_json->data->object->lines->data[0]->id);
		echo "<br />";
		echo ("** data->object->lines->data[0]->amount: " .  $event_json->data->object->lines->data[0]->amount);
		echo "<br />";
		echo ("data->object->lines->data[0]->period->start: " .  $event_json->data->object->lines->data[0]->period->start);
		echo "<br />";
		echo ("data->object->lines->data[0]->period->end: " .  $event_json->data->object->lines->data[0]->period->end);
		echo "<br />";
		echo ("data->object->lines->data[0]->period->end: " .  $event_json->data->object->lines->data[0]->period->end);
		echo "<br />";
		echo ("** data->object->lines->data[0]->plan->id: " .  $event_json->data->object->lines->data[0]->plan->id);
		
		
		
		foreach ($event_json as $obj) {
			//echo ($obj->created);
			//echo "<br />";
			
		}
		
		
	}
	
	
	public function CheckHolds () {
		
		//this function/route should run on xx day at xx time
		
		//check Holds table
		
		//first get the date for the "week of" - assuming this is going to run on Wednesday, it's always the previous Tuesday

		$WeekOfDate = new DateTime();
		$WeekOfDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
		$WeekOfDate->modify('Tuesday next week');
		
		echo $WeekOfDate->format('Y-m-d') . "<br />";
		$WeekOfDate_string = $WeekOfDate->format('Y-m-d');
		
		//query for all holds with $WeekOfDate_string in them
		$holds = Shippingholds::where('date_to_hold', $WeekOfDate_string)
								->where('hold_status', 'hold')
								->get();
								
		foreach ($holds as $hold) {

				//if there is a hold for the next week, then 
					// 1. Cancel Subscription in Stripe (add hold note)
					// 2. If there is a customer who is coming off of a hold, reactive their susbcription

				echo $hold->user_id;
				
				//cancel this user temporarily
				$this->CancelSubscription($hold->user_id);
				
				//only do this once! -- mark as "held" so this process will only run once
				$hold->hold_status='held';
				$hold->save();
				
		}
		
		//check for previous week holds and restore those users IF they don't have a hold for THIS week
		
		
	}
	
	public function IssueRefund($charge_id) {
		
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
		
		$re = \Stripe\Refund::create(array(
		  "charge" => $charge_id
		));
		
	}
	
	public function CancelSubscription ($id) {
		
		//permanently deactive an account
		//mark record as cancelled in Users, Subscriptions tables
		$user = User::where('id', $id)->first();
		$user->status="inactive";
		
		//retrieve stripe ID from subscriptions table
		$userSubscription = UserSubscription::where('user_id',$id)->first();
		$userSubscription->status = "cancelled";
		
		$stripe_sub_id = $userSubscription->stripe_id;
		
		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here https://dashboard.stripe.com/account/apikeys
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
				
		$subscription = \Stripe\Subscription::retrieve($stripe_sub_id);
		$subscription->cancel();
		
		$user->save();
		$userSubscription->save();	
		
		http_response_code(200);
		
	}
	
	public function RestartSubscription ($id) {
		
		//create a new subscription in Stripe
		
		//update subscriber ID in Users, Subscriptions
		
		//update statuses in Users, Subscriptions table
		
		$user = User::where('id', $id)->first();
		$user->status="active";
		$customer_stripe_id = $user->stripe_id;

		//retrieve stripe ID from subscriptions table
		$userSubscription = UserSubscription::where('user_id',$id)->first();
		$userSubscription->status = "active";
		$plan_id = $userSubscription->product_id;
		
		$product = Product::where('id', $plan_id)->first();
		$stripe_plan_id = $product->stripe_plan_id;
		
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
		
		$subscription = \Stripe\Subscription::create(array(
		  "customer" => $customer_stripe_id,
		  "plan" => $stripe_plan_id
		));
		
		$userSubscription->stripe_id = $subscription->id;
		$userSubscription->save();
		$user->save();
		
		http_response_code(200);
		
	}
	
	public function CheckForHold ($id, $holddate) {
		
		//remove hold from holds table
		$hold = Shippingholds::where('user_id', $id)
					->where('date_to_hold', $holddate)
					->where('hold_status', 'hold')
					->first();

		//if there is a hold
		if ($hold) {
			$returnJSON = '{"id":"' . $id . '","holddate": "' .  $holddate . '","holdstatus":"hold"}';
			return ($returnJSON);

		}else{
			$returnJSON = '{"id":"' . $id . '","holddate": "' .  $holddate . '","holdstatus":"notheld"}';
			return ($returnJSON);
		}
		
		
		
	}
	
	public function UnHoldSubscription ($id, $holddate) {
		
			
			//remove hold from holds table
			$hold = Shippingholds::where('user_id', $id)
						->where('date_to_hold', $holddate)
						->where('hold_status', 'hold')
						->first();

			//if there is a hold
			if ($hold) {
				$hold->hold_status = "released";
				$hold->save();
				
				//get the custoemr
				$user = User::where('id', $id)->first();
				$user->status="active";
				$customer_stripe_id = $user->stripe_id;
				
				//retrieve stripe ID from subscriptions table
				$userSubscription = UserSubscription::where('user_id',$id)->first();
				$userSubscription->status = "active";
				$plan_id = $userSubscription->product_id;

				$product = Product::where('id', $plan_id)->first();
				$stripe_plan_id = $product->stripe_plan_id;

				\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

				$subscription = \Stripe\Subscription::create(array(
				  "customer" => $customer_stripe_id,
				  "plan" => $stripe_plan_id
				));

				$userSubscription->stripe_id = $subscription->id;
				$userSubscription->save();
				$user->save();

			}

			http_response_code(200);
			return redirect('/delivery-schedule'); 
		
	}
	
	
	//NOTE - This function is probably not necessary, since after a subscription has been cancelled for the week, it can no longer be undone
	//INSTEAD, use the RestartSubscription function
	public function CommitUnHoldSubscription ($id, $holddate) {
		
		$user = User::where('id', $id)->first();
		$user->status="active";
		$customer_stripe_id = $user->stripe_id;

		//retrieve stripe ID from subscriptions table
		$userSubscription = UserSubscription::where('user_id',$id)->first();
		$plan_id = $userSubscription->product_id;
		
		$product = Product::where('id', $plan_id)->first();
		$stripe_plan_id = $product->stripe_plan_id;
		
		//remove hold from holds table
		$hold = Shippingholds::where('user_id', $id)
					->where('date_to_hold', $holddate)
					->where('hold_status', 'hold')
					->first();
		
		//if there is a hold
		if ($hold) {
			$hold->hold_status = "released";
			$hold->save();
		
		
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
		
			$subscription = \Stripe\Subscription::create(array(
		  	"customer" => $customer_stripe_id,
		  	"plan" => $stripe_plan_id
			));
		
			$userSubscription->stripe_id = $subscription->id;
			$userSubscription->save();
			$user->save();
		
		}
		
		http_response_code(200);
		
		
	}
	
	public function HoldSubscription ($id,$holddate) {
		
		//Record a Hold in the Database
		
		$hold = new Shippingholds;
		$hold->user_id = $id;
		$hold->date_to_hold = $holddate;
		$hold->hold_status = "hold";
		$hold->save();
		
		//if not, create one
		return redirect('/delivery-schedule'); 
	}
	
	
	public function CommitHoldSubscription ($id,$holddate) {
		
		//update status of Users, Subscription, Stripe
	
		$user = User::where('id', $id)->first();
		//user status stays "active" - only their susbcription changes

		//retrieve stripe ID from subscriptions table
		$userSubscription = UserSubscription::where('user_id',$id)->first();
		$userSubscription->status = "hold";

		$stripe_sub_id = $userSubscription->stripe_id;

		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here https://dashboard.stripe.com/account/apikeys
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

		$subscription = \Stripe\Subscription::retrieve($stripe_sub_id);
		$subscription->cancel();

		$user->save();
		$userSubscription->save();
		
		//add a record to the holds table
		//check to see if a hold exists for this week
		$hold = new Shippingholds;
		$hold->user_id = $id;
		$hold->date_to_hold = $holddate;
		$hold->hold_status = "hold";
		$hold->save();
	
		//if not, create one
		
	}
	
	public function testStripeInvoice() {
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,            "http://onepotato.app/stripe/webhook" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     '{
		  "created": 1326853478,
		  "livemode": false,
		  "id": "evt_00000000000000",
		  "type": "invoice.payment_succeeded",
		  "object": "event",
		  "request": null,
		  "pending_webhooks": 1,
		  "api_version": "2016-03-07",
		  "data": {
		    "object": {
		      "id": "in_00000000000000",
		      "object": "invoice",
		      "amount_due": 1200,
		      "application_fee": null,
		      "attempt_count": 1,
		      "attempted": true,
		      "charge": "_00000000000000",
		      "closed": true,
		      "currency": "usd",
		      "customer": "cus_00000000000000",
		      "date": 1467583569,
		      "description": null,
		      "discount": null,
		      "ending_balance": 0,
		      "forgiven": false,
		      "lines": {
		        "data": [
		          {
		            "id": "sub_8iBeLD4bRSABxM",
		            "object": "line_item",
		            "amount": 1498,
		            "currency": "usd",
		            "description": null,
		            "discountable": true,
		            "livemode": true,
		            "metadata": {
		            },
		            "period": {
		              "start": 1468187931,
		              "end": 1468792731
		            },
		            "plan": {
		              "id": "test_plan_1",
		              "object": "plan",
		              "amount": 1000,
		              "created": 1466701834,
		              "currency": "usd",
		              "interval": "week",
		              "interval_count": 1,
		              "livemode": false,
		              "metadata": {
		              },
		              "name": "Test Plan 1",
		              "statement_descriptor": null,
		              "trial_period_days": null
		            },
		            "proration": false,
		            "quantity": 1,
		            "subscription": null,
		            "type": "subscription"
		          }
		        ],
		        "total_count": 1,
		        "object": "list",
		        "url": "/v1/invoices/in_18TIj7CEzFD8tUOqQjZFX76H/lines"
		      },
		      "livemode": false,
		      "metadata": {
		      },
		      "next_payment_attempt": null,
		      "paid": true,
		      "period_end": 1467583131,
		      "period_start": 1466978331,
		      "receipt_number": null,
		      "starting_balance": 0,
		      "statement_descriptor": null,
		      "subscription": "sub_00000000000000",
		      "subtotal": 1200,
		      "tax": null,
		      "tax_percent": null,
		      "total": 1200,
		      "webhooks_delivered_at": 1467583569
		    }
		  }
		}' ); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

		$result=curl_exec ($ch);
		
		print_r($result);
	
	}


}
