<?php 
/* the purpose of this page is to display a form to allow a person to register 
 * the form will be sticky meaning if there is a mistake the data previously  
 * entered will be displayed again. Once a form is submitted (to this same page) 
 * we first sanitize our data by replacing html codes with the html character. 
 * then we check to see if the data is valid. if data is valid enter the data  
 * into the table and we send and dispplay a confirmation email message.  
 *  
 * if the data is incorrect we flag the errors. 
 *  
 * Written By: Robert Erickson robert.erickson@uvm.edu 
 * Last updated on: October 10, 2013 
 *  
 *  
  -- -------------------------------------------------------- 
  -- 
  -- Table structure for table `tblRegister` 
  -- 

  CREATE TABLE IF NOT EXISTS `tblRegister` ( 
  `pkRegisterId` int(11) NOT NULL AUTO_INCREMENT, 
  `fldEmail` varchar(65) DEFAULT NULL, 
  `fldDateJoined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, 
  `fldConfirmed` tinyint(1) NOT NULL DEFAULT '0', 
  `fldApproved` tinyint(4) NOT NULL DEFAULT '0', 
  PRIMARY KEY (`pkPersonId`) 
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; 

 * I am using a surrogate key for demonstration,  
 * email would make a good primary key as well which would prevent someone 
 * from entering an email address in more than one record. 
 */ 

//----------------------------------------------------------------------------- 
//  
// Initialize variables 
//   



$debug = false; 
if ($debug) print "<p>DEBUG MODE IS ON</p>"; 

$baseURL = "http://www.uvm.edu/~jdedward/"; 
$folderPath = "cs148/assignment4.1/"; 
// full URL of this form 
$yourURL = $baseURL . $folderPath . "register.php"; 

//$yourURL = "http://www.uvm.edu/~jdedward/cs148/assignment4.2/register.php";

require_once("connect.php"); 

//############################################################################# 
// set all form variables to their default value on the form. for testing i set 
// to my email address. you lose 10% on your grade if you forget to change it. 

$email = ""; 
$lastName = "";
$firstName = "";
$street = "";
$city = "";
$state = "";
$zip = "";
$dateJoined = "";
$confirmed = "";

//############################################################################# 
//  
// flags for errors 

$emailERROR = false; 
$lastNameERROR = false;
$firstNameERROR = false;
$streeERROR = false;
$cityERROR = false;
$stateERROR = false;
$zipERROR = false;
$dateJoinedERROR = false;
$confirmedERROR = false; 

//############################################################################# 
//   
$mailed = false; 
$messageA = ""; 
$messageB = ""; 
$messageC = ""; 


//----------------------------------------------------------------------------- 
//  
// Checking to see if the form's been submitted. if not we just skip this whole  
// section and display the form 
//  
//############################################################################# 
// minor security check 

if (isset($_POST["btnSubmit"])) { 
    $fromPage = getenv("http_referer"); 

   //if ($debug) 
       // print "<p>From: " . $fromPage . " should match "; 
       // print "<p>Your: " . $yourURL; 

    if ($fromPage != $yourURL) { 
        die("<p>Sorry you cannot access this page. Security breach detected and reported.</p>"); 
        } 


//############################################################################# 
// replace any html or javascript code with html entities 
// 

    $email = htmlentities($_POST["txtEmail"], ENT_QUOTES, "UTF-8"); 
    $lastName = htmlentities($_POST["txtlastName"], ENT_QUOTES, "UTF-8");
    $firstName = htmlentities($_POST["txtfirstName"], ENT_QUOTES, "UTF-8");
    $street = htmlentities($_POST["txtStreet"], ENT_QUOTES, "UTF-8");
    $city = htmlentities($_POST["txtCity"], ENT_QUOTES, "UTF-8");
    $state = htmlentities($_POST["txtState"], ENT_QUOTES, "UTF-8");
    $zip = htmlentities($_POST["txtZip"], ENT_QUOTES, "UTF-8");
    
    


//############################################################################# 
//  
// Check for mistakes using validation functions 
// 
// create array to hold mistakes 
//  

    include ("validation_functions.php"); 

    $errorMsg = array(); 


//############################################################################ 
//  
// Check each of the fields for errors then adding any mistakes to the array. 
// 
//^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^       Check email address 
    if (empty($email)) { 
        $errorMsg[] = "Please enter your Email Address"; 
        $emailERROR = true; 
    } else { 
        $valid = verifyEmail($email); /* test for non-valid  data */ 
        if (!$valid) { 
            $errorMsg[] = "I'm sorry, the email address you entered is not valid."; 
            $emailERROR = true; 
        } 
    } 
    
    if (empty($lastName)) {
        $errorMsg[] = "Please enter your last name.";
        $lastNameERROR = true;
    } else {
        $valid = verifyAlphaNum($lastName);
        if (!valid) {
            $errorMsg[] = "I'm sorry, please enter only text characters.";
            $lastNameERROR = true;
        }
    }
    
     if (empty($firstName)) {
        $errorMsg[] = "Please enter your first name.";
        $firstNameERROR = true;
    } else {
        $valid = verifyAlphaNum($firstName);
        if (!valid) {
            $errorMsg[] = "I'm sorry, please enter only text characters.";
            $firstNameERROR = true;
        }
    }
    
    if (empty($street)) {
        $errorMsg[] = "Please enter your street address.";
        $streetERROR = true;
    } else {
        $valid = verifyAlphaNum($firstName);
        if (!valid) {
            $errorMsg[] = "I'm sorry, please enter only text characters.";
            $streetERROR = true;
        }
    }
    
    if (empty($city)) {
        $errorMsg[] = "Please enter your city.";
        $cityERROR = true;
    } else {
        $valid = verifyAlphaNum($firstName);
        if (!valid) {
            $errorMsg[] = "I'm sorry, please enter only text characters.";
            $cityERROR = true;
        }
    }
    
    if (empty($state)) {
        $errorMsg[] = "Please enter your state.";
        $stateERROR = true;
    } else {
        $valid = verifyAlphaNum($firstName);
        if (!valid) {
            $errorMsg[] = "I'm sorry, please enter only text characters.";
            $stateERROR = true;
        }
    }
    
    if (empty($zip)) {
        $errorMsg[] = "Please enter your first name.";
        $zipERROR = true;
    } else {
        $valid = verifyAlphaNum($firstName);
        if (!valid) {
            $errorMsg[] = "I'm sorry, please enter only text characters.";
            $zipERROR = true;
        }
    }
    

//############################################################################ 
//  
// Processing the Data of the form 
// 

    if (!$errorMsg) { 
        if ($debug) print "<p>Form is valid</p>"; 

//############################################################################ 
// 
// the form is valid so now save the information 
//     
        $primaryKey = ""; 
        $dataEntered = false; 
         
        try { 
            $db->beginTransaction(); 
            
            $sql = 'INSERT INTO tblRegisterUser SET fldUserEmail="' . $email . '", fldlastName="' . $lastName . '", fldfirstName="' . $firstName . '", fldStreet="' . $street . '", fldCity="' . $city . '", fldState="' . $state . '", fldZip="' . $zip . '"';
            $stmt = $db->prepare($sql); 
            if ($debug) print "<p>sql ". $sql; 
        
            $stmt->execute(); 
             
            $primaryKey = $db->lastInsertId(); 
            if ($debug) print "<p>pk= " . $primaryKey; 

            // all sql statements are done so lets commit to our changes 
            $dataEntered = $db->commit(); 
            if ($debug) print "<p>transaction complete "; 
        } catch (PDOExecption $e) { 
            $db->rollback(); 
            if ($debug) print "Error!: " . $e->getMessage() . "</br>"; 
            $errorMsg[] = "There was a problem with accpeting your data please contact us directly."; 
        } 


        // If the transaction was successful, give success message 
        if ($dataEntered) { 
            if ($debug) print "<p>data entered now prepare keys "; 
            //################################################################# 
            // create a key value for confirmation 

            $sql = "SELECT fldDateJoined FROM tblRegisterUser WHERE pkUserID=" . $primaryKey; 
            $stmt = $db->prepare($sql); 
            $stmt->execute(); 

            $result = $stmt->fetch(PDO::FETCH_ASSOC); 
             
            $dateSubmitted = $result["fldDateJoined"]; 

            $key1 = sha1($dateSubmitted); 
            $key2 = $primaryKey; 

            if ($debug) print "<p>key 1: " . $key1; 
            if ($debug) print "<p>key 2: " . $key2; 


            //################################################################# 
            // 
            //Put forms information into a variable to print on the screen 
            // 

            $messageA = '<h2>Thank you for registering.</h2>'; 

            $messageB = "<p>Click this link to confirm your registration: "; 
            $messageB .= '<a href="' . $baseURL . $folderPath  . 'confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . '">Confirm Registration</a></p>'; 
            $messageB .= "<p>or copy and paste this url into a web browser: "; 
            $messageB .= $baseURL . $folderPath  . 'confirmation.php?q=' . $key1 . '&amp;w=' . $key2 . "</p>"; 

            $messageC .= "<p><b>Email Address:</b><i>   " . $email . "</i></p>"; 

            //############################################################## 
            // 
            // email the form's information 
            // 
             
            $subject = "Thank you for registering"; 
            include_once('mailMessage.php'); 
            $mailed = sendMail($email, $subject, $messageA . $messageB . $messageC); 
        } //data entered    
    } // no errors  
}// ends if form was submitted.  

    include ("top.php"); 

    $ext = pathinfo(basename($_SERVER['PHP_SELF'])); 
    $file_name = basename($_SERVER['PHP_SELF'], '.' . $ext['extension']); 

    print '<body id="' . $file_name . '">'; 

    include ("header.php"); 
    //include ("menu.php");
    include ("nav.php");
    ?> 


<body id ="home">
    
    
    
    <aside id="other">
        <h2>Please take the time to register!</h2>
    <p> Upon registering you will be eligible for special discounts on a variety of items. 
        All that is needed is some basic information. Maxine Davis Glass Art will not distribute 
        your information to outside sources. It is purely confidential and will be stored securely.
        <br>
        Once you have registered you will receive an email. This email will prompt you to confirm your 
        registration to Maxine Davis Glass Art. Once that is completed you will receive another email indicating
        confirmation. This allows us to more accurately collect data from our users. 
    </p>
    
    </aside>
    
    <section id="main"> 
        <h2>Register </h2> 
            <p> This is the registration form. Required fields are noted with an asterisk.
                When you have completed the form please select register. 
                Any errors in your entries will be noted in red. Once all errors 
                are corrected select register again and your information will be entered 
                into the database.</p>
        
        
    

        <? 
//############################################################################ 
// 
//  In this block  display the information that was submitted and do not  
//  display the form. 
// 
        if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { 
            print "<h2>Your Request has "; 

            if (!$mailed) { 
                echo "not "; 
            } 

            echo "been processed</h2>"; 

            print "<p>A copy of this message has "; 
            if (!$mailed) { 
                echo "not "; 
            } 
            print "been sent to: " . $email . "</p>"; 

            echo $messageA . $messageC; 
        } else { 


//############################################################################# 
// 
// Here we display any errors that were on the form 
// 

            print '<div id="errors">'; 

            if ($errorMsg) { 
                echo "<ol>\n"; 
                foreach ($errorMsg as $err) { 
                    echo "<li>" . $err . "</li>\n"; 
                } 
                echo "</ol>\n"; 
            } 

            print '</div>'; 
            ?> 
            <!--   Take out enctype line    --> 
            <form action="<? print $_SERVER['PHP_SELF']; ?>" 
                  
                  method="post" 
                  id="frmRegister"> 
                <fieldset class="contact"> 
                    <legend>Contact Information</legend> 

                    <label class="required" for="txtEmail">Email </label> 

                    <input id ="txtEmail" name="txtEmail" class="element text medium<?php if ($emailERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $email; ?>" placeholder="enter your preferred email address" onfocus="this.select()"  tabindex="30"/> 
                    
                    <label class="required" for="txtlastName">Last Name </label> 
                    
                    <input id ="txtlastName" name="txtlastName" class="element text medium<?php if($lastNameERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $lastName; ?>" placeholder="enter your last name" onfocus="this.select()" tabindex="30"/>
                    
                    <label class="required" for="txtfirstName">First Name </label> 
                    
                    <input id ="txtfirstName" name="txtfirstName" class="element text medium<?php if($firstNameERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $firstName; ?>" placeholder="enter your first name" onfocus="this.select()" tabindex="30"/>
                    
                    <label class="required" for="txtStreet">Street </label> 
                    
                    <input id ="txtstreet" name="txtStreet" class="element text medium<?php if($streetERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $street; ?>" placeholder="enter your street" onfocus="this.select()" tabindex="30"/>
                    
                    <label class="required" for="txtCity">City </label> 
                    
                    <input id ="txtcity" name="txtCity" class="element text medium<?php if($cityERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $city; ?>" placeholder="enter your city" onfocus="this.select()" tabindex="30"/>
                
                    <label class="required" for="txtState">State </label> 
                    
                    <input id ="txtstate" name="txtState" class="element text medium<?php if($stateERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $state; ?>" placeholder="enter your state" onfocus="this.select()" tabindex="30"/>
                
                    <label class="required" for="txtZip">Zip</label> 
                    
                    <input id ="txtzip" name="txtZip" class="element text medium<?php if($zipERROR) echo ' mistake'; ?>" type="text" maxlength="255" value="<?php echo $zip; ?>" placeholder="enter your zip" onfocus="this.select()" tabindex="30"/>
                
                
                
                
                
                </fieldset>  


                <fieldset class="buttons"> 
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Register" tabindex="991" class="button"> 
                    <input type="reset" id="butReset" name="butReset" value="Reset Form" tabindex="993" class="button" onclick="reSetForm()" > 
                </fieldset>                     

            </form> 
            <?php 
        } // end body submit 
        if ($debug) 
            print "<p>END OF PROCESSING</p>"; 
        ?> 
    </section> 


    


    <? 
    include ("footer.php"); 
    ?> 

</body> 
<!enctype="multipart/form-data">
</html>
