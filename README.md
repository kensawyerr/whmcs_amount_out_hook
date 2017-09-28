# whmcs_fees_hook
Add your configured fees to WHMCS invoices when they are paid. This way you can have accurate records in your monthly transactions that will show your running cost and your profit.


## Getting Started

Modify the variables $tlds and $products to set fees for your TLDs and fees for your products. The fees here refer to any expenses you will make in order to provide the service. E.g actual domain registration fees.

For Example:
If you sell .com domains for $20 and .net domains for $25 on your website but the actual cost you registar the domains at the register is $15 (for .com) and $20 (for .net) then the $tlds array should look like so:
    
	$tlds = array(
        ".com" => "15",
        ".net" => "20"
    );
	
The same goes for the $products variable. This should be an array of your product IDs and whatever total cost you spend to provide it to your client.

### Prerequisites

You need to have WHMCS installed


### Installing

Step 1: Modify add_fees_hook.php with your configuration of the $tlds and $products variables.
Step 2: Please add_fees_hook.php in /includes/hooks/ directory of your WHMCS installation