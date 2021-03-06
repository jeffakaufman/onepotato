<?php

namespace App\Http\Controllers;

use App\AC_Mediator;
use Illuminate\Http\Request;
use App\Subinvoice;

use App\User;
use App\Credit;
use App\Shipping_address;
use App\Csr_note;
use App\UserSubscription;
use App\Product;
use App\SimpleLogger;

use App\Referral;
use App\Order;
use App\Cancellation;
use DateTime;
use Date;
use App\Shippingholds;
use DateTimeZone;
use DB;
use Auth;


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


	public function getOrderXMLTest () {
		
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
					//$order = new Order;
				    // $order->save();

					//get the OrderID from the order table
					//add arbitrary number to the primary key to obfuscate order ids

					//$order_id = $order->id + 11565;
					//$order->order_id = $order_id;
					// $order->save();	
					$order_id = "12345";
			
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
						//$invoice->save();
						
					}//end if
				} //end foreach
		
		$ship_xml .= '</Orders>';
			
		//update invoices table
		//update orders table
	
		//record XML in log table
		
		
		//echo XML
		return $ship_xml;
		
		
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
								//$dietary_prefs_string = $subscriber->getDietaryPreferencesAttribute($subscriber->dietary_preferences);
								$dietary_prefs_string = $subscriber->getNutFreeOrGlutenFree();
								
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
		//$ship_date = str_replace('/', '-', $shipnotice->ShipDate);
		$ship_date = $shipnotice->ShipDate;
		$ship_date_format = date('Y-m-d', strtotime($ship_date));
		
		$order->ship_date = $ship_date_format;
		$order->ship_carrier = $shipnotice->Carrier;
		$order->ship_service = $shipnotice->Service;
		$order->tracking_number = $shipnotice->TrackingNumber;
		$order->ship_station_xml = $shipXML;
		$order->save();
		
		//mark subinvoice as "shipped"
		$invoice = Subinvoice::where('order_id',$order_id)->first();
		$invoice->invoice_status = "shipped";
		$invoice->ship_date = $ship_date_format;
		$invoice->ship_carrier = $shipnotice->Carrier;
		$invoice->ship_service = $shipnotice->Service;
		$invoice->tracking_number = $shipnotice->TrackingNumber;
		$invoice->ship_station_xml = $shipXML;
		
		$invoice->save();
	

       $ac = AC_Mediator::GetInstance();
        try {
            $user = User::find($invoice->user_id);
           $ac->MenuShipped($user, $shipnotice->TrackingNumber);
        } catch(\Exception $e) {
            //TODO :: Add storing AC tracking ID
       }

		
		http_response_code(200); // PHP 5.4 or greater
	}
	
	public function testShippingStatus () {
		
	
		$shipXML = '<?xml version="1.0" encoding="utf-8"?>

		<ShipNotice>

		  <OrderNumber>123456</OrderNumber>

		  <OrderID>123456</OrderID>

		  <CustomerCode>customer@mystore.com</CustomerCode>

		  <LabelCreateDate>12/8/2011 12:56 PM</LabelCreateDate>

		  <ShipDate>11/1/2016</ShipDate>

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
		
		//echo $shipXML;
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL,            "http://onepotato.kidevelopment.com/shipstation/getorders" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     $shipXML);
		
			curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

			$result=curl_exec ($ch);

			//print_r($result);
		
	}
	
	public function recordStripeInvoice() {

	    $now = new DateTime('now');
	    $logger = new SimpleLogger("stripe_payment_{$now->format('Ymd')}.log");

        $logger->Log("Webhook Registered");
			// Set your secret key: remember to change this to your live secret key in production
			// See your keys here https://dashboard.stripe.com/account/apikeys
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

			// Retrieve the request's body and parse it as JSON
			$input = @file_get_contents("php://input");
			$event_json = json_decode($input);

        if(!$event_json) {
            $logger->Log("Unable to decode input :: {$input}");
        }

//file_put_contents(__DIR__."/../../../storage/logs/stripe.log", $input);
		// Do something with $event_json

		//record the invoice data to the database


		//record the RAW JSON to the log (need to create)

		//record the parsed JSON to the database


        switch($event_json->type) {
            case 'invoice.payment_failed':
                $logger->Log("Payment failed");
                $this->_invoicePaymentFailed($event_json, $logger);
                break;

            case 'invoice.payment_succeeded':
            default:
                $logger->Log("Payment successful");
                $this->_invoicePaymentSucceeded($event_json);
                break;
        }

		return http_response_code(200); // PHP 5.4 or greater
	}

    private function _invoicePaymentFailed($event_json, SimpleLogger $logger) {
        // Need to send data to Active Campaign
        // By adding tag 'CC-Fail' to the correct customer
        // We are able to get him by subscription_id from stripe
        // also need to do something with "Payment Fail Count" -- looks like we need to check this and increment, may be field will be added to DB later, but can't find it now
        $user = $this->_getUser($event_json);

        if($user instanceof User) {
            $logger->Log("User recognized :: #{$user->id} {$user->email} {$user->name}");
            $ac = AC_Mediator::GetInstance();

            try {
                $ac->PaymentFailed($user);
                $logger->Log("Processed OK");
            } catch (\Exception $e) {
                $logger->Log("Problem :: {$e->getMessage()}");
            }
        } else {
            $logger->Log("Unable to find user :: ".json_encode($event_json));
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

		//*** new code to get the last item in the array
		$invoiceitemCount = $event_json->data->object->lines->total_count;
		$lastItem = $invoiceitemCount - 1;

        $subinvoice->stripe_sub_id = $event_json->data->object->lines->data[$lastItem]->id;

        $period_start_date_unix = $event_json->data->object->lines->data[$lastItem]->period->start;
        $period_end_date_unix = $event_json->data->object->lines->data[$lastItem]->period->end;

        $period_start_date_str = date("c", $period_start_date_unix);
        $period_start_date = new DateTime($period_start_date_str);
        $period_start_date_formatted = date_format($period_start_date,"Y-m-d H:i:s");

        $period_end_date_str = date("c", $period_end_date_unix);
        $period_end_date = new DateTime($period_end_date_str);
        $period_end_date_formatted = date_format($period_end_date,"Y-m-d H:i:s");

        $subinvoice->period_start_date = $period_start_date_formatted;
        $subinvoice->period_end_date = $period_end_date_formatted;

        $stripe_id = $event_json->data->object->lines->data[$lastItem]->id;

        //coupon code (may not exist)
        if (isset($event_json->data->object->discount->coupon->id)) {
            $subinvoice->coupon_code = $event_json->data->object->discount->coupon->id;
        }


        $subinvoice->charge_amount = $event_json->data->object->lines->data[$lastItem]->amount;

		if (isset($event_json->data->object->amount_due)) {
			$subinvoice->charge_actual = $event_json->data->object->amount_due;
		}
		
        $subinvoice->plan_id = $event_json->data->object->lines->data[$lastItem]->plan->id;


		//loop over invoice items
		if (isset( $event_json->data->object->lines->total_count)) {
			$invoiceitemCount = $event_json->data->object->lines->total_count;
		}else{
			$invoiceitemCount = 0;
		}
	
		$invoice_charges_total = 0;
		
		for ($i = 0; $i < $invoiceitemCount; $i++) {
	 		$line_item_type = $event_json->data->object->lines->data[$i]->type;
			if ($line_item_type=="invoiceitem") {
				
				$item_charge = $event_json->data->object->lines->data[$i]->amount;
			
				$invoice_charges_total += $item_charge; 
				//echo "<br />" . $invoice_charges . " <br />";
			
			}
		}
		$subinvoice->invoiceitem_charges = $invoice_charges_total;

        $subinvoice->invoice_status = "charged_not_shipped";
        $subinvoice->raw_json = json_encode($event_json);


        //link user_id

        $subscriber = UserSubscription::where('stripe_id',$stripe_id)->first();
        if($subscriber) { 
			$subinvoice->user_id = $subscriber->user_id; 
		}
		

        $subinvoice->save();
    }



    private function _invoicePaymentSucceeded_old($event_json) {
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

		if($subscriber) { 
			$subinvoice->user_id = $subscriber->user_id; 
		}
       

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
		  "id": "evt_191LMQCEzFD8tUOqTv1akbxh",
		  "object": "event",
		  "api_version": "2016-06-15",
		  "created": 1475696846,
		  "data": {
		    "object": {
		      "id": "in_191KQ8CEzFD8tUOqxIFnZW4O",
		      "object": "invoice",
		      "amount_due": 5478,
		      "application_fee": null,
		      "attempt_count": 1,
		      "attempted": true,
		      "charge": "ch_191LMPCEzFD8tUOqg4igBwRz",
		      "closed": true,
		      "currency": "usd",
		      "customer": "cus_93vW7IKx9vTa9H",
		      "date": 1475693232,
		      "description": null,
		      "discount": null,
		      "ending_balance": 0,
		      "forgiven": false,
		      "lines": {
		        "object": "list",
		        "data": [
		          {
		            "id": "ii_18zxnRCEzFD8tUOq8ncdzQrx",
		            "object": "line_item",
		            "amount": -1000,
		            "currency": "usd",
		            "description": "test",
		            "discountable": false,
		            "livemode": true,
		            "metadata": {},
		            "period": {
		              "start": 1475367937,
		              "end": 1475367937
		            },
		            "plan": null,
		            "proration": false,
		            "quantity": null,
		            "subscription": null,
		            "type": "invoiceitem"
		          },
		          {
		            "id": "ii_18zZ69CEzFD8tUOqHKFktfMH",
		            "object": "line_item",
		            "amount": -500,
		            "currency": "usd",
		            "description": "Test Amount",
		            "discountable": false,
		            "livemode": true,
		            "metadata": {},
		            "period": {
		              "start": 1475272997,
		              "end": 1475272997
		            },
		            "plan": null,
		            "proration": false,
		            "quantity": null,
		            "subscription": null,
		            "type": "invoiceitem"
		          },
		          {
		            "id": "ii_18zZ1dCEzFD8tUOqCweEmI7r",
		            "object": "line_item",
		            "amount": -216,
		            "currency": "usd",
		            "description": "Test",
		            "discountable": false,
		            "livemode": true,
		            "metadata": {},
		            "period": {
		              "start": 1475272717,
		              "end": 1475272717
		            },
		            "plan": null,
		            "proration": false,
		            "quantity": null,
		            "subscription": null,
		            "type": "invoiceitem"
		          },
		          {
		            "id": "sub_9EjBiCNeulD9J6",
		            "object": "line_item",
		            "amount": 7194,
		            "currency": "usd",
		            "description": null,
		            "discountable": true,
		            "livemode": true,
		            "metadata": {},
		            "period": {
		              "start": 1475693155,
		              "end": 1476297955
		            },
		            "plan": {
		              "id": "omn_2_adults_1",
		              "object": "plan",
		              "amount": 7194,
		              "created": 1471983233,
		              "currency": "usd",
		              "interval": "week",
		              "interval_count": 1,
		              "livemode": true,
		              "metadata": {},
		              "name": "Omnivore Box for 2 Adults",
		              "statement_descriptor": null,
		              "trial_period_days": null
		            },
		            "proration": false,
		            "quantity": 1,
		            "subscription": null,
		            "type": "subscription"
		          }
		        ],
		        "has_more": false,
		        "total_count": 4,
		        "url": "/v1/invoices/in_191KQ8CEzFD8tUOqxIFnZW4O/lines"
		      },
		      "livemode": true,
		      "metadata": {},
		      "next_payment_attempt": null,
		      "paid": true,
		      "period_end": 1475693155,
		      "period_start": 1475088355,
		      "receipt_number": null,
		      "starting_balance": 0,
		      "statement_descriptor": null,
		      "subscription": "sub_9EjBiCNeulD9J6",
		      "subtotal": 5478,
		      "tax": null,
		      "tax_percent": null,
		      "total": 5478,
		      "webhooks_delivered_at": 1475693232
		    }
		  },
		  "livemode": true,
		  "pending_webhooks": 1,
		  "request": null,
		  "type": "invoice.payment_succeeded"
		}';
		
		$event_json = json_decode($input);
		
			//*** new code to get the last item in the array
			$invoiceitemCount = $event_json->data->object->lines->total_count;
			$lastItem = $invoiceitemCount - 1;
		
		echo $event_json->data->object->lines->data[$lastItem]->id;
		
		foreach ($event_json as $obj) {
			//echo ($obj->created);
			//echo "<br />";
			
		}
		
		
	}
	
	
	//issues a line itme for a credit 
	public function issueCredit ($id, Request $request) {
		
		//get the business 
		$credit_amount = $request->credit_amount;
		$credit_type = $request->credit_type;
		$credit_description = $request->credit_description;
		$apply_credit_amount = 0;
		
		//get user 
		$user = User::where('id', $id)->first();
	
		
        if($user) {
	
			
			$stripe_customer_id = $user->stripe_id;
			
			
			//figure out amount to credit 
			if ($credit_type=="amount") {
				
				//make it a negative amount 
				//adjust to pennies
				$apply_credit_amount = -1 * abs($credit_amount*100);
				
			}
			
			if ($credit_type=="percent") {
				
				//get the user's subscription 
				$userSubscription = UserSubscription::where('user_id',$id)->first();
													
				$productID = $userSubscription->product_id;
				
				$userProduct = Product::where('id', $productID)->first();
				
				//figure out monthly value
				$product_price = $userProduct->cost;
				
				//apply percentage
				//adjust to pennies
				$apply_credit_amount = round(($product_price * ($credit_amount/100)) * 100);
				
				//make it a negative amount 
				$apply_credit_amount = -1 * abs($apply_credit_amount);
				
			}
			
			//issue credit
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
			
			\Stripe\InvoiceItem::create(array(
		  		"customer" => $stripe_customer_id,
		  		"amount" => $apply_credit_amount,
		  		"currency" => "usd",
		  		"description" => $credit_description
			));
			
			//record credit in database
			$credit = new Credit;
			$credit->user_id = $id;
			if ($credit_type=="amount") {
				$credit->credit_amount = abs($apply_credit_amount);
			}
			if ($credit_type=="percent") {
				$credit->credit_percent = $credit_amount;
				$credit->credit_amount = abs($apply_credit_amount);
			}
			$credit->credit_description = $credit_description;
			$credit->credit_status = "applied_to_stripe";
			$credit->save();			
           
        }	

		return redirect("/admin/user_details/" . $id);
		
}
	
	
	//handle credits - check for a credit when invoice created is called from STRIPE
	
	public function checkForCredits () {
		
			\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

			// Retrieve the request's body and parse it as JSON
			$input = @file_get_contents("php://input");
			$event_json = json_decode($input);
			
			//get the user record based on the 
			$user = $this->_getUser($event_json);

	        if($user instanceof User) {
	            	
				//When invoice is created, check to see if there's an unapplied credit
				//use the user id to see if there is a record in credits table
				//if there is a credit in the database with status 'recorded', apply it via stripe
				$credit = Credit::where('user_id', $user->id)
										->where('credit_status', 'recorded_not_applied')
										->first();
			
				
				if ($credit) {
					
					echo "Credit Found for user " . $user->id;
					
	        		$invoice_id = $event_json->data->object->id;
					$customer_id = $event_json->data->object->customer;
					$invoice_amount = $event_json->data->object->amount_due;
					
					echo "Invoice amount: " . $invoice_amount;
					
					//figure out the amount  
					$creditAmount = $credit->credit_amount;
					$creditPercent = $credit->credit_percent;
					
					if (!empty($creditAmount)) {
						//there is a value in $creditAmount in CENTS - that's the negative invoice amount
						$apply_credit_amount = $creditAmount;
						
					}
					
					if (!empty($creditPercent)) {
						//there is a value in $credit percent overrides credit_amount
						$apply_credit_amount = $invoice_amount * $credit_percent;
						
					}
					
					if (isset($apply_credit_amount)) {
						
						//make apply credit amount negative
						$apply_credit_amount = -1 * abs($apply_credit_amount);
						
					}else{
						//there is no recorded credit amount
						$apply_credit_amount = 0;
					}
					
					echo "Credit Amount: " . $apply_credit_amount;
					
					/* example: 
					\Stripe\InvoiceItem::create(array(
					  "customer" => "cus_3R1W8PG2DmsmM9",
					  "invoice" => "in_3ZClIXPhhwkNsp",
					  "amount" => 1000,
					  "currency" => "usd",
					  "description" => "One-time setup fee"
					));
					*/
				
					//record CREDIT in Stripe via discount
					
					try {
						\Stripe\InvoiceItem::create(array(
					  		"customer" => $customer_id,
					  		"invoice" => $invoice_id,
					  		"amount" => $apply_credit_amount,
					  		"currency" => "usd",
					  		"description" => $credit->credit_description
								));
					
							$credit->date_applied = date("Y-m-d H:i:s");  
							$credit->credit_status = 'credit_applied';
							$credit->stripe_xml = json_encode($event_json);
							$credit->save();
							http_response_code(200);
					
					} catch (\Stripe\Error\Base $e) {
							  // Code to do something with the $e exception object when an error occurs
					
							$credit->date_applied = date("Y-m-d H:i:s");  
							$credit->credit_status = 'stripe_error_not_applied';
							$credit->stripe_xml = json_encode($event_json);
							$credit->save();
						
							echo($e->getMessage());
							http_response_code(500);
							
					} catch (\Exception $e) {
						  	// Something else happened, completely unrelated to Stripe
							$credit->date_applied = date("Y-m-d H:i:s");  
							$credit->credit_status = 'stripe_error_not_applied';
							$credit->stripe_xml = json_encode($event_json);
							$credit->save();
							http_response_code(500);
					} 
			
				}//end if there is a credit
			
			
			}  //end if $user
		
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
		//leaving the users active JK 10/2/2016
		//$user = User::where('id', $id)->first();
		//$user->status = User::STATUS_INACTIVE_CANCELLED;
		
		//retrieve stripe ID from subscriptions table
		$userSubscription = UserSubscription::where('user_id',$id)->first();
		$userSubscription->status = "cancelled";
		
		$stripe_sub_id = $userSubscription->stripe_id;
		
		// Set your secret key: remember to change this to your live secret key in production
		// See your keys here https://dashboard.stripe.com/account/apikeys
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
				
		$subscription = \Stripe\Subscription::retrieve($stripe_sub_id);
		$subscription->cancel();
		
		//$user->save();
		$userSubscription->save();
		
		$cancel = new Cancellation();

		$cancel->user_id = $id;
		$cancel->cancel_reason = "manual cancel";
			
		$cancel->save();	
		
		http_response_code(200);
		
	}
	

	public function RestartSubscription ($id) {
		
		//create a new subscription in Stripe
		
		//update subscriber ID in Users, Subscriptions
		
		//update statuses in Users, Subscriptions table
		
		$user = User::where('id', $id)->first();
		$user->status = User::STATUS_ACTIVE;
		$customer_stripe_id = $user->stripe_id;

		//retrieve stripe ID from subscriptions table
		$userSubscription = UserSubscription::where('user_id',$id)->first();
		$userSubscription->status = "active";
		$plan_id = $userSubscription->product_id;
		
		$product = Product::where('id', $plan_id)->first();
		$stripe_plan_id = $product->stripe_plan_id;
		
		\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
		
		$trial_ends_date = $this->GetTrialEndsDateForRestart();
		
		$subscription = \Stripe\Subscription::create(array(
		  "customer" => $customer_stripe_id,
		  "plan" => $stripe_plan_id,
		  "trial_end" => $trial_ends_date,
		));
		
		$userSubscription->stripe_id = $subscription->id;
		$userSubscription->save();
		$user->save();

        Auth::login($user, true);
        return redirect("/account");


		//http_response_code(200);
//		return redirect('/login');


// 3) I changed the system so when user reactivates a cancelled account are now sent to the login page instead of a blank screen.
// Please change that so they are logged in and send to /account. When i tried it I got an ugly error message.
// 4) For reactivation, the start date should be Tuesday if it is before midnight on Wednesday.
// If it is after midnight, it should be a week from Tuesday.
// The credit cards are processed at Midnight on Wednesdays, so this starts them the first week.

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
	
	
	
	public function ReleaseAllHoldsByDate ($holddate) {
		
			$holds = Shippingholds::where('date_to_hold', $holddate)
						->where('hold_status', 'held')
						->get();
						
			foreach ($holds as $hold) {
				echo "UserId: " . $hold->user_id . "<br />";
				$this->ProcessUnHoldSubscription_Stripe($hold->user_id, $holddate);
			}
		
	}
	
	
	
	//to be run to restart a cancelled subscriber who has skipped a week
	//and been cancelled in stripe
	public function ProcessUnHoldSubscription_Stripe ($id, $holddate) {
		
			
			//remove hold from holds table
			$hold = Shippingholds::where('user_id', $id)
						->where('date_to_hold', $holddate)
						->where('hold_status', 'held')
						->first();

			//if there is a hold
			if ($hold) {
				$hold->hold_status = "released-after-hold";
				$hold->save();
				
				//get the custoemr
				$user = User::where('id', $id)->first();
				$user->status = User::STATUS_ACTIVE;
				$customer_stripe_id = $user->stripe_id;
			
				//retrieve stripe ID from subscriptions table
				$userSubscription = UserSubscription::where('user_id',$id)->first();

				$userSubscription->status = "active";
				$plan_id = $userSubscription->product_id;

				$product = Product::where('id', $plan_id)->first();
				$stripe_plan_id = $product->stripe_plan_id;

				\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
				
				$trial_ends_date = $this->GetTrialEndsDate();

				$subscription = \Stripe\Subscription::create(array(
				  "customer" => $customer_stripe_id,
				  "plan" => $stripe_plan_id,
				  "trial_end" => $trial_ends_date,
				));

				$userSubscription->stripe_id = $subscription->id;
				$userSubscription->save();
				$user->save();

			}

		
	}
	
	//to be run to restart a cancelled subscriber who has skipped a week
	//and been cancelled in stripe
	//returns user to delivery schedule
	public function ProcessUnHoldSubscription ($id, $holddate) {
		
			
			//remove hold from holds table
			$hold = Shippingholds::where('user_id', $id)
						->where('date_to_hold', $holddate)
						->where('hold_status', 'held')
						->first();

			//if there is a hold
			if ($hold) {
				$hold->hold_status = "released-after-hold";
				$hold->save();
				
				//get the custoemr
				$user = User::where('id', $id)->first();
				$user->status = User::STATUS_ACTIVE;
				$customer_stripe_id = $user->stripe_id;
			
				//retrieve stripe ID from subscriptions table
				$userSubscription = UserSubscription::where('user_id',$id)->first();

				$userSubscription->status = "active";
				$plan_id = $userSubscription->product_id;

				$product = Product::where('id', $plan_id)->first();
				$stripe_plan_id = $product->stripe_plan_id;

				\Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
				
				$trial_ends_date = $this->GetTrialEndsDate();

				$subscription = \Stripe\Subscription::create(array(
				  "customer" => $customer_stripe_id,
				  "plan" => $stripe_plan_id,
				  "trial_end" => $trial_ends_date,
				));

				$userSubscription->stripe_id = $subscription->id;
				$userSubscription->save();
				$user->save();

			}

			http_response_code(200);
			//return redirect('/delivery-schedule'); 
		
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
				/*
				$user = User::where('id', $id)->first();
				$user->status = User::STATUS_ACTIVE;
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
				*/

			}

			http_response_code(200);
			return redirect('/delivery-schedule'); 
		
	}
	
	
	//NOTE - This function is probably not necessary, since after a subscription has been cancelled for the week, it can no longer be undone
	//INSTEAD, use the RestartSubscription function
	public function CommitUnHoldSubscription ($id, $holddate) {
		
		$user = User::where('id', $id)->first();
		$user->status = User::STATUS_ACTIVE;
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

		curl_setopt($ch, CURLOPT_URL,            "https://onepotato.kidevelopment.com/stripe/webhook" );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($ch, CURLOPT_POST,           1 );
		curl_setopt($ch, CURLOPT_POSTFIELDS,     '{
		  "id": "evt_18wKgQCEzFD8tUOqOALdV5bC",
		  "object": "event",
		  "api_version": "2016-06-15",
		  "created": 1474502602,
		  "data": {
		    "object": {
		      "id": "in_18wJkKCEzFD8tUOqBfDZYoFU",
		      "object": "invoice",
		      "amount_due": 8401,
		      "application_fee": null,
		      "attempt_count": 1,
		      "attempted": true,
		      "charge": "ch_18wKgQCEzFD8tUOqsCRnFCeQ",
		      "closed": true,
		      "currency": "usd",
		      "customer": "cus_99DdrELqLYjsFi",
		      "date": 1474499000,
		      "description": null,
		      "discount": null,
		      "ending_balance": 0,
		      "forgiven": false,
		      "lines": {
		        "object": "list",
		        "data": [
		          {
		            "id": "ii_18w32kCEzFD8tUOqEversVLL",
		            "object": "line_item",
		            "amount": -1047,
		            "currency": "usd",
		            "description": "Unused time on Omnivore Box for 2 Adults and 2 Children after 21 Sep 2016",
		            "discountable": false,
		            "livemode": true,
		            "metadata": {},
		            "period": {
		              "start": 1474434794,
		              "end": 1474498800
		            },
		            "plan": {
		              "id": "omn_2_adults_2_child_1",
		              "object": "plan",
		              "amount": 9894,
		              "created": 1471983311,
		              "currency": "usd",
		              "interval": "week",
		              "interval_count": 1,
		              "livemode": true,
		              "metadata": {},
		              "name": "Omnivore Box for 2 Adults and 2 Children",
		              "statement_descriptor": null,
		              "trial_period_days": null
		            },
		            "proration": true,
		            "quantity": 1,
		            "subscription": "sub_99DduOPa4vJyXp",
		            "type": "invoiceitem"
		          },
		          {
		            "id": "ii_18w32kCEzFD8tUOqAc5QtVsh",
		            "object": "line_item",
		            "amount": 904,
		            "currency": "usd",
		            "description": "Remaining time on Omnivore Box for 2 Adults and 1 Child after 21 Sep 2016",
		            "discountable": false,
		            "livemode": true,
		            "metadata": {},
		            "period": {
		              "start": 1474434794,
		              "end": 1474498800
		            },
		            "plan": {
		              "id": "omn_2_adults_1_child_1",
		              "object": "plan",
		              "amount": 8544,
		              "created": 1471983280,
		              "currency": "usd",
		              "interval": "week",
		              "interval_count": 1,
		              "livemode": true,
		              "metadata": {},
		              "name": "Omnivore Box for 2 Adults and 1 Child",
		              "statement_descriptor": null,
		              "trial_period_days": null
		            },
		            "proration": true,
		            "quantity": 1,
		            "subscription": "sub_99DduOPa4vJyXp",
		            "type": "invoiceitem"
		          },
		          {
		            "id": "sub_99DduOPa4vJyXp",
		            "object": "line_item",
		            "amount": 8544,
		            "currency": "usd",
		            "description": null,
		            "discountable": true,
		            "livemode": true,
		            "metadata": {},
		            "period": {
		              "start": 1474498800,
		              "end": 1475103600
		            },
		            "plan": {
		              "id": "omn_2_adults_1_child_1",
		              "object": "plan",
		              "amount": 8544,
		              "created": 1471983280,
		              "currency": "usd",
		              "interval": "week",
		              "interval_count": 1,
		              "livemode": true,
		              "metadata": {},
		              "name": "Omnivore Box for 2 Adults and 1 Child",
		              "statement_descriptor": null,
		              "trial_period_days": null
		            },
		            "proration": false,
		            "quantity": 1,
		            "subscription": null,
		            "type": "subscription"
		          }
		        ],
		        "has_more": false,
		        "total_count": 3,
		        "url": "/v1/invoices/in_18wJkKCEzFD8tUOqBfDZYoFU/lines"
		      },
		      "livemode": true,
		      "metadata": {},
		      "next_payment_attempt": null,
		      "paid": true,
		      "period_end": 1474498800,
		      "period_start": 1473894000,
		      "receipt_number": null,
		      "starting_balance": 0,
		      "statement_descriptor": null,
		      "subscription": "sub_99DduOPa4vJyXp",
		      "subtotal": 8401,
		      "tax": null,
		      "tax_percent": null,
		      "total": 8401,
		      "webhooks_delivered_at": 1474499000
		    }
		  },
		  "livemode": true,
		  "pending_webhooks": 1,
		  "request": null,
		  "type": "invoice.payment_succeeded"
		}' ); 
		curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: text/plain')); 

		$result=curl_exec ($ch);
		
		print_r($result);
	
	}


	public function GetTrialEndsDate() {

		date_default_timezone_set('America/Los_Angeles');
		
        //use start date - find the previous Wednesday at 16:00:00

        //figure out date logic for trial period -
        // - mist be UNIX timestamp
			
        $trial_ends = "";
			
        //time of day cutoff for orders
        $cutOffTime = "16:00:00";
        $cutOffDay = "Wednesday";
			
        //change dates to WEDNESDAY
        //cutoff date is the last date to change or to signup for THIS week
        $cutOffDate = new \DateTime("this {$cutOffDay} {$cutOffTime}");

        //get today's date
        $today = new \DateTime('now');
        $currentDay = $today->format("l");
        $currentTime = $today->format("H:is");
			
        //echo "Today is " . $currentDay . "<br />";
			
        //echo "Cut off date: " . $cutOffDate->format('Y-m-d H:i:s') . "<br />";
        //echo "Current time: " . $todaysDate->format('Y-m-d H:i:s') . "<br />";

        //THIS IS ALL OLD CODE _ SINCE WE KNOW THE START DATE, we can just use that as the
        //check to see if today is the same day as the cutoff day
        if ($currentDay == $cutOffDay) {
				
            //check to see if it's BEFORE the cutoff tine. If so, then this is a special case
            if ($currentTime < $cutOffTime) {
                //ok, so it's the day of the cutoff, but before time has expired
                //SET the trial_ends date to $cutOffDate - no problem
                //echo "You have JUST beat the cutoff period <br /> Setting the trial_ends to today";
                $trial_ends = $cutOffDate;
            } else {
                //the cutoff tiem has just ended
                //now, set the date to NEXT $cutOffDate
                $trial_ends = new \DateTime("next {$cutOffDay} {$cutOffTime}");
                //echo "You have missed the cutoff period <br /> Setting the trial_ends to next week";
            }
        } else {
            //today is not the same as the trial ends date, so simply set the date to the next cutoff
            $trial_ends = $cutOffDate;
        }

        return ($trial_ends->getTimestamp());
		
        //echo "Trial Ends: " . $trial_ends->format('Y-m-d H:i:s')  . "<br />";
			
        //echo "UNIX version of timestamp: " . $trial_ends->getTimestamp() . "<br />";
		
//			$TestDate = new DateTime('@1470463200');
//			$TestDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
			//echo "Converted back:" . $TestDate->format('Y-m-d H:i:s') . "<br />";
			
	}

	public function GetTrialEndsDateForRestart() {

// 4) For reactivation, the start date should be Tuesday if it is before midnight on Wednesday.
// If it is after midnight, it should be a week from Tuesday.
// The credit cards are processed at Midnight on Wednesdays, so this starts them the first week.

		date_default_timezone_set('America/Los_Angeles');

        // - must be UNIX timestamp

        //time of day cutoff for orders
        $cutOffTime = "16:00:00";
        $cutOffDay = "Wednesday";

        //change dates to WEDNESDAY
        //cutoff date is the last date to change or to signup for THIS week
        $cutOffFull = new \DateTime("this {$cutOffDay} {$cutOffTime}");
        $cutOffDate = new \DateTime("this {$cutOffDay}");

        //get today's date
        $now = new \DateTime('now');
        $today = new \DateTime('today');

        $triadEnds = (clone($cutOffDate))->modify('this tuesday');
        //echo "Today is " . $currentDay . "<br />";

        //echo "Cut off date: " . $cutOffDate->format('Y-m-d H:i:s') . "<br />";
        //echo "Current time: " . $todaysDate->format('Y-m-d H:i:s') . "<br />";

        //THIS IS ALL OLD CODE _ SINCE WE KNOW THE START DATE, we can just use that as the
        //check to see if today is the same day as the cutoff day
        if ($today == $cutOffDate) {

            //check to see if it's BEFORE the cutoff tine. If so, then this is a special case
            if ($now < $cutOffFull) {
                //ok, so it's the day of the cutoff, but before time has expired
                //SET the trial_ends date to $cutOffDate - no problem
                //echo "You have JUST beat the cutoff period <br /> Setting the trial_ends to today";

                //DO NOTHING
            } else {
                //the cutoff time has just ended
                //now, set the date to NEXT $cutOffDate
                //echo "You have missed the cutoff period <br /> Setting the trial_ends to next week";

                $triadEnds->modify("+1 week");
            }
        } else {
            //today is not the same as the trial ends date, so simply set the date to the next cutoff

            //DO NOTHING
        }

        return ($triadEnds->getTimestamp());

        //echo "Trial Ends: " . $trial_ends->format('Y-m-d H:i:s')  . "<br />";

        //echo "UNIX version of timestamp: " . $trial_ends->getTimestamp() . "<br />";

//			$TestDate = new DateTime('@1470463200');
//			$TestDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
			//echo "Converted back:" . $TestDate->format('Y-m-d H:i:s') . "<br />";

	}
	
	//utility function to pull data out of the XML stored in subinvoices and insert into rows
	public function FixSubInvoices () {
	
		//get all the subinvoices
		$subinvoices = Subinvoice::all();
		$invoice_charges_total = 0;
		//loop over them, find the amount_due
		foreach ($subinvoices as $subinvoice) {
			
			//echo $subinvoice->id . "<br />";
		
			$invoice_json = json_decode($subinvoice->raw_json);
			
			//loop over invoice items
			if (isset( $invoice_json->data->object->lines->total_count)) {
				$invoiceitemCount = $invoice_json->data->object->lines->total_count;
			}else{
				$invoiceitemCount = 0;
			}
		
			for ($i = 0; $i < $invoiceitemCount; $i++) {
		 		$line_item_type = $invoice_json->data->object->lines->data[$i]->type;
				if ($line_item_type=="invoiceitem") {
					echo $invoice_json->data->object->lines->data[$i]->id . "<br />";
					$item_charge = $invoice_json->data->object->lines->data[$i]->amount;
					echo " Item Charge: " . $item_charge . "<br />";
					$invoice_charges_total += $item_charge; 
					//echo "<br />" . $invoice_charges . " <br />";
					$subinvoice->invoiceitem_charges = $invoice_charges_total;
					echo "TEST: " . $subinvoice->invoiceitem_charges . "<br />";
					$subinvoice->save();
				}
			}
		
			
			//$subinvoice->invoice_charges = $invoice_charges_total;
			
			echo "Invoice Charges Total: " . $invoice_charges_total . "<br />";
		    $invoice_charges_total = 0;
		
		}
		
		
	}
	
	public function TestOrderXMLUser ($invoiceid) {
		
			/****
			gets order XML for a particular SubInvoice Row for testing purposes

			****/
			
			
		//get date ranges
		//get today's date
		$todaysDate = new DateTime();
		$todaysDate->setTimeZone(new DateTimeZone('America/Los_Angeles'));
		$todaysDate_format = date_format($todaysDate, "Y-m-d H:i:s");
		
		
		$ship_xml = '<?xml version="1.0" encoding="utf-8"?><Orders>';
		
		//retrieve all orders with status of "<strong>charged_not_shipped</strong>" from invoice table
		$subinvoices = Subinvoice::where('id',$invoiceid)->get();
		
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
					//$order = new Order;
				    // $order->save();

					//get the OrderID from the order table
					//add arbitrary number to the primary key to obfuscate order ids

					//$order_id = $order->id + 11565;
					//$order->order_id = $order_id;
					// $order->save();
					
					//fake order ID for testing	
					$order_id = "12345";
			
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
							//echo "SUBCRIBER PREFS: " . $subscriber->dietary_preferences;
							if (isset($subscriber->dietary_preferences)) {
								
								//$dietary_prefs_string = $subscriber->getDietaryPreferencesAttribute($subscriber->dietary_preferences);
								$dietary_prefs_string = $subscriber->getNutFreeOrGlutenFree();
								
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
						//$invoice->save();
						
					}//end if
				} //end foreach
		
		$ship_xml .= '</Orders>';
			
		//update invoices table
		//update orders table
	
		//record XML in log table
		
		
		//echo XML
		echo $ship_xml;
		
	}

}
