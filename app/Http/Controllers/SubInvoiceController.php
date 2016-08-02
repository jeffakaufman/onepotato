<?php

namespace App\Http\Controllers;

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

	public function getOrderXML() {
		
			/****
			UPDATE query to only select invoices who fall in the correct date range

			ADD pagination feature for large volumes of orders

			****/
		
		
		$ship_xml = '<?xml version="1.0" encoding="utf-8"?><Orders>';
		
		//retrieve all orders with status of "charged_not_shipped" from invoice table
		$subinvoices = Subinvoice::where('invoice_status','charged_not_shipped')->get();
		
		//loop over the invoices to create Order records
		
		foreach ($subinvoices as $invoice) {
			
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
				$charge_date_formatted = $charge_date->format('m/d/Y H:i');	
				
					$subscriber = UserSubscription::where('stripe_id',$stripe_id)->first();
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
				$ship_xml .= "<CustomField1></CustomField1>";
				$ship_xml .= "<CustomField2><![CDATA[" . $subscriber->dietary_preferences . "]]></CustomField2>";
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
			
			//$invoice->invoice_status = "sent_to_ship";
				$invoice->save();
			
			
				$ship_xml .= "</Order>";
			
			}
		}
		
		$ship_xml .= '</Orders>';
			
		//update invoices table
		//update orders table
	
		//record XML in log table
		
		
		//echo XML
		return $ship_xml;
		
		
	}
	
	public function updateShippingStatus(Request $request) {
		
		$shipXML = @file_get_contents("php://input");
		
		
		$shipnotice = simplexml_load_string($shipXML);
		
		$order = Order::where('order_id',$shipnotice->OrderID)->first();
		
		//convert the date
		$order->ship_date = $shipnotice->ShipDate;
		$order->ship_carrier = $shipnotice->Carrier;
		$order->ship_service = $shipnotice->Service;
		$order->tracking_number = $shipnotice->TrackingNumber;
		$order->ship_station_xml = $shipXML;
		$order->save();
		
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

		// Do something with $event_json
		
		//record the invoice data to the database
		
		
		//record the RAW JSON to the log (need to create)
		
		//record the parsed JSON to the database
		$subinvoice = new Subinvoice;
		
		$subinvoice->stripe_event_id = $event_json->id;
		$subinvoice->charge_date = date("Y-m-d H:i:s", $event_json->data->object->date);
		$subinvoice->stripe_customer_id = $event_json->data->object->customer;
		$subinvoice->stripe_sub_id = $event_json->data->object->lines->data[0]->id;
		$stripe_id = $event_json->data->object->lines->data[0]->id;
		
		$subinvoice->charge_amount = $event_json->data->object->lines->data[0]->amount;
		$subinvoice->plan_id = $event_json->data->object->lines->data[0]->plan->id;
		$subinvoice->invoice_status = "charged_not_shipped";
		$subinvoice->raw_json = $input;
	
		
		//link user_id
		
		$subscriber = UserSubscription::where('stripe_id',$stripe_id)->first();
		$subinvoice->user_id = $subscriber->user_id;
		
		$subinvoice->save();
		
		http_response_code(200); // PHP 5.4 or greater
		
		
	}
	
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
