<?php
if( isset($_POST) ){
    $userIP = $_SERVER["REMOTE_ADDR"];
    $recaptchaResponse = $_POST['g-recaptcha-response'];
    $secretKey = "6Ldf__4SAAAAANgLHkFpwjGQ4pRyV_Fccj-FEE10";
    $request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}&remoteip={$userIP}");

    if(!strstr($request, "true")){
        echo "Are you a robot? If not, please confirm this on the previous page.";
    } else {
        //form validation vars
        $formok = true;
        $errors = array();

        //sumbission data
        $ipaddress = $_SERVER['REMOTE_ADDR'];
        $date = date('d/m/Y');
        $time = date('H:i:s');

        //form data
        $name = $_POST['name'];    
        $email = $_POST['email'];
        $type = $_POST['type'];
        $length = $_POST['length'];
        $additions = $_POST['additions'];

        //validate name is not empty
        if(empty($name)){
            $formok = false;
            $errors[] = "You have not entered a name";
        }

        //validate email address is not empty
        if(empty($email)){
            $formok = false;
            $errors[] = "You have not entered an email address";
            //validate email address is valid
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $formok = false;
            $errors[] = "You have not entered a valid email address";
        }

        //send email if all is ok
        if($formok){
            $headers = "From: enquiries@kingaudio.co.uk" . "\r\n";
            $headers .= 'Content-type: text; charset=iso-8859-1' . "\r\n";

            $emailbody = 
                "Enquiry from website:

Name:
{$name}				
Email Address: 
{$email}
Type:          
{$type}				
Length:        
{$length}

Additional info: 
{$additions}

At {$date} {$time}.";

mail("enquiries@kingaudio.co.uk,james@jamespking.com","--- Transcription Quote Request ---",$emailbody,$headers);

        } 

        //what we need to return back to our form
        $returndata = array(
            'form_ok' => $formok,
            'errors' => $errors
        );


        //if this is not an ajax request
        if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'){
            //set session variables
            session_start();
            $_SESSION['cf_returndata'] = $returndata;

            //redirect back to form
            header('location: ' . $_SERVER['HTTP_REFERER'] . '?success');
        }
    }
}
