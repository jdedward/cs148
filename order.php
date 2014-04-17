<?php
include("top.php");
include("header.php");
include("nav.php");



//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// 
// Initialize variables
//  
//  Here we set the default values that we want our form to display

$debug = false;

if(isset($_GET["debug"])){
    $debug = false;
}

if ($debug) print "<p>DEBUG MODE IS ON</p>";

//
//  CHANGES NEEDED: create variable for each form element
//                  set your default values

$orderName="";
$email="";
$chkBowl = false;
$chkPlate = false;
$chkOutput="";
$color="Red";
$specialInstructions="";
$giftWrap="No";

// this would be the full url of your form
$yourURL = "http://www.uvm.edu/~jdedward/cs148/assignment4.1/order.php";


//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
// 
// initialize flags for errors, one for each item
//
// CHANGES NEEDED: create variable for each form element that we can check
// use same variable name as above and add ERROR (just a good naming convention
//

$orderNameERROR = false;
$emailERROR = false;
$chkBowlERROR = false;
$chkPlateERROR = false; 
$colorERROR = false;
$specialInstructionsERROR = false;
$giftWrapERROR = false;



$mailed = false;
$message = "";



//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
// 
// This if statement is how we can check to see if the form has been submitted
// 
// NO CHANGES: but VERFIY your forms submit button is named btnSubmit

if (isset($_POST["btnSubmit"])){

    //******************************************************************
    // is the refeering web page the one we want or is someone trying 
    // to hack in. this is not 100% reliable but ok for our purposes   */
    //
    // Security check block one, no changes needed
    
    $fromPage = getenv("http_referer"); 

    if ($debug) print "<p>From: " . $fromPage . " should match yourUrl: " . $yourURL;

    if($fromPage != $yourURL){
        die("<p>Sorry you cannot access this page. Security breach detected and reported</p>");
    } 
    
    require_once("connect.php"); 
    
    
    //************************************************************
    // we need to make sure there is no malicious code so we do 
    // this for each element we pass in. Be sure your names match
    // your objects
    // 
    // Security check block two
    // 
    // What this does is take things like <script> and replace it
    // with &lt;script&gt; so that hackers cannot send malicous 
    // code to you.
    //   
    // You will notice i have set radio buttons, list box and the 
    // check boxes just in case someone tries something funky.
    // 
    // CHANGES NEEDED: match PHP variables with form elements
    // 
    // */
    
    $orderName = htmlentities($_POST["txtOrderName"],ENT_QUOTES,"UTF-8");
    $email = htmlentities($_POST["txtEmail"],ENT_QUOTES,"UTF-8");
    $specialInstructions = htmlentities($_POST["txtSpecialInstructions"],ENT_QUOTES,"UTF-8");
    
    if(isset($_POST["bowCategory"])) {
        $chkBowl  = true;
        $chkOutput="Bowl";
    }else{
        $chkBowl  = false;
        //$chkOutput="Plate";
    }
    
    if(isset($_POST["plaCategory"])) {
        $chkPlate  = true;
        $chkOutput="Plate";
    }else{
        $chkPlate  = false;
        //$chkOutput="Bowl";
    }
    
    
    

    $giftWrap = htmlentities($_POST["radGiftWrap"],ENT_QUOTES,"UTF-8");
    $color = htmlentities($_POST["lstColors"],ENT_QUOTES,"UTF-8");
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // 
    // Begin looking for mistakes after we have done the above security checks
    //
    // the error msg array is going to be used to hold our mistakes if there 
    // are any. the array can expand to hold as much as we need.
    // CHANGES NEEDED: 
    //                 Be sure to create the file: validation_functions.php
    // 
    
    
    include ("validation_functions.php"); // you need to create this file (see link in lecture notes)
    
    $errorMsg=array();
    
    //#######################################################
    // we are going to put our forms data into this array so we can save it
    // NO CHANGES NEEDED
    $dataRecord=array();
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // 
    // Now start checking each one. I am only doing one here but the IF block
    // is pretty important as it gives you the structure of what to to do.
    //
    // CHANGES NEEDED: 
    //                 Be sure you change $firstName to match your variables 
    //                 The IF block would be copied for each item you are
    //                 checking.
    //                 You would need to change the second IF to refelct the
    //                 function or condition you are checking for
    //    
    
    // Test first name for empty and valid characters
    if(empty($orderName)){
       $errorMsg[]="Please enter a name for the order (i.e. 'Birthday Present')";
       $orderNameERROR = true;
    } else {
       $valid = verifyAlphaNum ($orderName); /* test for non-valid  data */
       if (!$valid){ 
           $errorMsg[]="Order Name must be letters and numbers, spaces, dashes and single quotes only.";
           $orderNameERROR = true;  
       }
    }
    
    if(empty($email)){
       $errorMsg[]="Please enter a valid email address.";
       $emailERROR = true;
    } else {
       $valid = verifyEmail ($email); /* test for non-valid  data */
       if (!$valid){ 
           $errorMsg[]="The email address you entered is not valid.";
           $emailERROR = true;  
       }
    }
    if(empty($chkOutput)){
        $errorMsg[]="Please select 'bowl' or 'plate'";
        $chkBowlERROR = true;
        $chkPlateERROR = true; 
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    // 
    // our form data is valid at this point so we can process the data
    if(!$errorMsg){	
        if ($debug) print "<p>Form is valid</p>";
        
        //####################################################################
        //
        // Begin processing data
        //####################################################################

        $primaryKey = ""; 
        $dataEntered = false; 
         
        try { 
            $db->beginTransaction(); 
            
            $sql = 'INSERT INTO tblOrder SET fldOrderName="' . $orderName . '", fldSpecialInstructions="' . $specialInstructions . '", fldCategory="' . $chkOutput . '", fldGiftWrap="' . $giftWrap . '", fldColor="' . $color . '"';
            $stmt = $db->prepare($sql); 
            if ($debug) print "<p>sql ". $sql; 
        
            $stmt->execute(); 
             
            $primaryKey = $db->lastInsertId(); 
            if ($debug) print "<p>pk= " . $primaryKey; 

            
            
            
            $sql = 'INSERT INTO tblCustomer SET fldCustomerEmail="' . $email . '"';
            $stmt = $db->prepare($sql); 
            if ($debug) print "<p>sql ". $sql; 
        
            $stmt->execute(); 
            
            
            $primaryKey2 = $db->lastInsertId();
            if ($debug) print "<p>pk2= " . $primaryKey2; 
            
            
            
            $sql = 'INSERT INTO tblCustomerOrder SET fkCustomerID="' . $primaryKey2 . '", fkOrderID="' .$primaryKey .'"';
            if ($debug) print "<p>sql ". $sql;

            $stmt = $db->prepare($sql);
            
            $stmt->execute(); 

            // all sql statements are done so lets commit to our changes 
            $dataEntered = $db->commit(); 
            if ($debug) print "<p>transaction complete "; 
        } catch (PDOExecption $e) { 
            $db->rollback(); 
            if ($debug) print "Error!: " . $e->getMessage() . "</br>"; 
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly."; 
        } 
        

        
        
        
        
        
        
        
       
         
        //************************************************************
        //
        //  In this block I am just putting all the forms information
        //  into a variable so I can print it out on the screen
        //
        //  the substr function removes the 3 letter prefix
        //  preg_split('/(?=[A-Z])/',$str) add a space for the camel case
        //  see: http://stackoverflow.com/questions/4519739/split-camelcase-word-into-words-with-php-preg-match-regular-expression
        //
        //  CHANGES: first message line. foreach no changes needed

        $message  = '<h2>Order information.</h2>';

        foreach ($_POST as $key => $value){
            $message .= "<p>"; 

            $camelCase = preg_split('/(?=[A-Z])/',substr($key,3));

            foreach ($camelCase as $one){
                $message .= $one . " ";
            }
            $message .= " = " . $value . "</p>";
        }
        
        //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
        //
        // since I have all the forms information i am going to mail it
        //
        
        $subject = "Thank you for ordering.";
        include_once('mailMessage.php');
        $mailed = sendMail($email, $subject, $message);
                  
    } // no errors 

} 

// ends if form was submitted. We will be adding more information ABOVE this



?>
<!DOCTYPE html>
<html lang="en">
<head>

<title>Order Form</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="author" content="James D. Edwards III">

<meta name='description' content="Example of a Form">

<link rel="stylesheet"
href="style.css"
type="text/css"
media="screen">

</head>
<body id="home">
    
    <aside id="other">
    <h2>Order a hand made piece of glass art today!</h2>
        <p> Art for you! Hand made by the one and only Maxine Davis. Fill out the order
            form by selecting the options that you would like. Upon submitting your order you
            will receive an email containing the order information. When you get this email you can be 
            sure we have received your order and will begin to hand make your art shortly.
        </p>
    
    </aside>
<? 
//*****************************************************************************
//
//  In this block  display the information that was submitted and do not 
//  display the form.
//  
//  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//  NO CHANGES NEEDED but the if condition is different than last time
//
if (isset($_POST["btnSubmit"])AND empty($errorMsg)){  // closing of if marked with: end body submit
    //$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$
    print "<h1>Your Request has ";

    if (!$mailed) {
        echo "not ";
    }
    
    echo "been processed</h1>";

    print "<p>A copy of this message has ";
    if (!$mailed) {
        echo "not ";
    }
    print "been sent</p>";
    print "<p>To: " . $email . "</p>";
    print "<p>Mail Message:</p>";
    echo $message;
    
} else {

// display the form, notice the closing } bracket at the end just before the 
// closing body tag
 
    
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// Here we display any errors that were on the form
//
    
print '<div id="errors">';

if($errorMsg){
    echo "<ol>\n";
    foreach($errorMsg as $err){
        echo "<li>" . $err . "</li>\n";
    }
    echo "</ol>\n";
} 

print '</div>';

?>

    <!-- notice we are sending the form to itself 
    
    Notice each input tag has a php echo that prints out our variable. 
    This is how we set the default values for each element
    
    CHANGES NEEDED MAke sure the variable names match the variables you 
    Initialized on the first few lines -->
    <section id ="main">
        <h2>Order Form</h2>
       <p> Maxine Davis Glass Art likes to give each order a name that the customer can pick. Instead of a randomly generated
  number enter anything into the order name section that you would like. Maybe "Birthday Present" or "Christmas Gift". 
  The special instruction section allows you to tell us here at Maxine Davis Glass Art anything you would like us to take
  note of specifically. It's entirely up to you and if for some reason we are unclear about what you would like we will 
  contact you to remedy the situation. 
       </p>
        
        
    
    <form action="<? print $_SERVER['PHP_SELF']; ?>" 
      method="post"
      id="frmOrder">
			
<fieldset class="wrapper">
 
  <p>Please fill out the following form to place an order. Required fields <span class='required'></span>.</p>

<fieldset class="intro">
<legend>Please complete </legend>

<fieldset class="contact">
<legend>Order Information</legend>					
	<label for="txtorderName" class="required">Order Name</label>
  	<input type="text" id="txtOrderName" name="txtOrderName" 
               <?php if($orderNameERROR) echo 'class="mistake"'; ?>
               value="<?php echo $orderName; ?>" 
    		tabindex="100" maxlength="25" placeholder="enter the order name" autofocus onfocus="this.select()" >
				
	<label for="txtEmail" class="required">Email</label>
  	<input type="email" id="txtEmail" name="txtEmail" value="<?php echo $email; ?>"
    		tabindex="110" maxlength="45" placeholder="enter a valid email address" onfocus="this.select()" >
        
        <label for="txtSpecialInstructions">Special Instructions:</label>
  	<input type="text" id="txtSpecialInstructions" name="txtSpecialInstructions" value="<?php echo $specialInstructions; ?>"
    		tabindex="110" maxlength="100" placeholder="enter any special instructions" onfocus="this.select()" >

</fieldset>					

<fieldset class="checkbox">
	<legend>Which category would you like? (check only one):</legend>
  	<label><input type="checkbox" id="chkBowl" name="bowCategory" value="Bowl" tabindex="221" 
			<?php if($chkBowl) echo ' checked="checked" ';?>> Bowl</label>
            
	<label><input type="checkbox" id="chkPlate" name="plaCategory" value="Plate" tabindex="223" 
			<?php if($chkPlate) echo ' checked="checked" ';?>> Plate</label>
        
</fieldset>

<fieldset class="radio">
	<legend>Gift Wrap?</legend>
	<label><input type="radio" id="radYes" name="radGiftWrap" value="Yes" tabindex="231" 
			<?php if($giftWrap=="Yes") echo ' checked="checked" ';?>>Yes</label>
            
	<label><input type="radio" id="radNo" name="radGiftWrap" value="No" tabindex="233" 
			<?php if($giftWrap=="No") echo ' checked="checked" ';?>>No</label>
</fieldset>

<fieldset class="lists">	
	<legend>What Color would you like your item to be? </legend>
	<select id="lstColors" name="lstColors" tabindex="281" size="1">
		<option value="Red" <?php if($color=="Red") echo ' selected="selected" ';?>>Red</option>
		<option value="Orange" <?php if($color=="Orange") echo ' selected="selected" ';?>>Orange</option>
		<option value="Yellow" <?php if($color=="Yellow") echo ' selected="selected" ';?>>Yellow</option>
                <option value="Green" <?php if($color=="Green") echo 'selected="selected" ';?>>Green</option>
                <option value="Blue" <?php if($color=="Blue") echo 'selected="selected" ';?>>Blue</option>
                <option value="Indigo" <?php if($color=="Indigo") echo 'selected="selected" ';?>>Indigo</option>
                <option value="Violet" <?php if($color=="Violet") echo 'selected="selected" ';?>>Violet</option>
	</select>
</fieldset>

<fieldset class="buttons">
	<legend></legend>				
	<input type="submit" id="btnSubmit" name="btnSubmit" value="Submit" tabindex="991" class="button">

	<input type="reset" id="butReset" name="butReset" value="Reset Form" tabindex="993" class="button" onclick="reSetForm()" >
</fieldset>					

</fieldset>
</fieldset>
</form>
    </section>
    

<?php
} // end body submit NO CHANGE NEEDED
if ($debug) print "<p>END OF PROCESSING</p>";

include("footer.php");
?>
</body>
</html>
