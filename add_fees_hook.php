<?php
use Illuminate\Database\Capsule\Manager as Capsule;
/**
 * Add fees to invoices when they have been marked as paid.
 *
 * Developer: sawyerrken (sawyerrken@gmail.com)
 * Github: https://github.com/sawyerrken/whmcs_fees_hook
 */

	


function sk_get_tld($domain){
    $domain_parts = explode(".", $domain);

    if(count($domain_parts)==2){
        $tld = ".".$domain_parts[1];
    }

    if(count($domain_parts)==3){
        $tld = ".".$domain_parts[1].".".$domain_parts[2];
    }
    return $tld;
}


add_hook('InvoicePaid', 1, function ($vars)
{
	

    /*CONFIGURATION 
	* How this works:
	* This script runs as a hook when invoice is paid successfully, fees are gotten from 
	* each invoice item based on what you have configured. This will help you know they
	* actual amount you get as profit and what you spend for fees like domain registration fees
	* and any other fees you pay for each service.
	* the configuration will reside in two arrays to avoid make any changes to the database
	* Array "tlds" will hold all your TLDs and their actual cost (as fees).
	* Array "products" will hold your product IDs and actual cost (as fees), if you run your server
	* or a reseller account, you may not need to set any cost for products. if the product is a VPS with cPanel
	* you could add the cPanel license cost as part of the fees
	*/

    //TLDs array in the format tld=>cost
    //the TLDs should be in lowercase. Configure as many TLDs as you have
    $tlds = array(
        ".com" => "10",
        ".net" => "12"
    );

    //products array in the format product_id => cost. Configure as many product IDs as you have on your WHMCS
    $products = array(
        "14"=>"30",
        "20"=>"45"
    );

    //=====Config Ends =======


    //pull the invoice id from the params
    $invoice_id = $vars['invoiceid'];


    //get invoice items belonging to the invoide id
	
    $total_fees = 0;

    //go through each invoice item in the invoice and get the type of service
    foreach(Capsule::table('tblinvoiceitems')->where('invoiceid', $invoice_id)->get() as $row){

        if($row->type == "DomainTransfer" || $row->type=="DomainRegister"){
                $domain_details = Capsule::table('tbldomains')->where('id', $row->relid)->first();
                $tld = strtolower(sk_get_tld($domain_details->domain));
                if(array_key_exists($tld, $tlds)){
                    $fee = $tlds[$tld];
                }
                else{
                    $fee = 0;
                }
				
		}
            
		if($row->type == "Hosting"){
		$hosting_details = Capsule::table('tblhosting')->where('id', $row->relid)->first();
		if(array_key_exists($hosting_details->packageid, $products)){
			$product_id = $hosting_details->packageid;
							fwrite($fh, "product id:".$product_id);

			$fee = $products[$product_id];

		}
		else{
			$fee = 0;
		}
		
		fwrite($fh, "product fee:".$fee);
		}

        

        $total_fees += $fee;

    }
	
	
	

    //update invoice with the total fees
	Capsule::table('tblaccounts')->where('invoiceid', $invoice_id)->update(['fees'=>$total_fees]);
	
});