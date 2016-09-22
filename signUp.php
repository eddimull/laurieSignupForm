<?php
require 'vendor/autoload.php';
use Mailgun\Mailgun;

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();


if(isset($_FILES['resume']))
{
# Instantiate the client.
$mgClient = new Mailgun($_ENV['MAILGUN_API']);
$domain = $_ENV['MAILGUN_DOMAIN'];

$uploadedFile = getcwd() . '/uploads/' . $_POST['last_name'] . '_' . $_FILES['resume']['name'];
move_uploaded_file($_FILES['resume']['tmp_name'],$uploadedFile);
# Make the call to the client.
$result = $mgClient->sendMessage("$domain",
                  array('from'    => $_ENV['RESUME_FROM'],
                        'to'      => $_ENV['RESUME_TO'],
                        'subject' => $_POST['first_name'] . ' ' . $_POST['last_name'] . ' submitted their resumé',
                        'html'    => $_POST['first_name'] . ' ' . $_POST['last_name'] . '\'s resumé is attached. You can reach them at <a:href="mailto:' . $_POST['email'] .'>' . $_POST['email'] . '</a>'
                  ), array("attachment" => array($uploadedFile))
                  );

	if($result->http_response_code === 200)
	{
		$mgClient->sendMessage("$domain",
							array('from' => $_ENV['NOREPLY'],
									'to' => $_POST['email'],
									'subject' => "Resumé submitted",
									'text' => 'Thank you for sending me your resumé. I\'ll get back with you as soon as possible!')
							);
		echo json_encode(['success'=>true]);
	}
	else
	{
		echo json_encode(['success'=>false]);
	}
}
else
{
	?>

	<!DOCTYPE html>
	<html>

	<head>
	    <title>Sign up</title>
	</head>

	<body>
	    <link rel='stylesheet' type='text/css' href='signup-form.css'>
	    <div class="ctct-embed-signup" style="font: 16px Helvetica Neue, Arial, sans-serif; font: 1rem Helvetica Neue, Arial, sans-serif; line-height: 1.5; -webkit-font-smoothing: antialiased;">
	        <div style="color:#5b5b5b; background-color:#e8e8e8; border-radius:5px;">
	            <span id="success_message" style="display:none;">
	<div style="text-align:center;">Thanks for sending me your information! You should receive an email confirmation that your information was received.</div>
	</span>
	            <form id="signupForm" data-id="embedded_signup:form" class="ctct-custom-form Form" name="embedded_signup" method="POST" action="signUp.php"  enctype="multipart/form-data">
	                <h2 style="margin:0;">Let's get started!</h2> Please fill in all the fields to get started with your resumé
	                <p data-id="Email Address:p">
	                    <label data-id="Email Address:label" data-name="email" class="ctct-form-required">Email Address</label>
	                    <input data-id="Email Address:input" required type="text" name="email" value="" maxlength="80">
	                </p>
	                <p data-id="First Name:p">
	                    <label data-id="First Name:label" data-name="first_name" class="ctct-form-required">First Name</label>
	                    <input data-id="First Name:input" required type="text" name="first_name" value="" maxlength="50">
	                </p>
	                <p data-id="Last Name:p">
	                    <label data-id="Last Name:label" data-name="last_name" class="ctct-form-required">Last Name</label>
	                    <input data-id="Last Name:input" required  type="text" name="last_name" value="" maxlength="50">
	                </p>	                
	                <p data-id="Resume:p">
	                    <label data-id="resume:label" data-name="last_name" class="ctct-form-required">Resumé</label>
	                    <input data-id="resume:input" required type="file" value="" name="resume">
	                </p>

	                <button type="submit" class="Button ctct-button Button--block Button-secondary" data-enabled="enabled">Sign Up</button>
	                <div>
	                    <p class="ctct-form-footer">By submitting this form, you are granting: LaurieJJames.com, LLC, 310 S Philo Drive, Lafayette, Louisiana, 70506, United States, http://www.lauriejjames.com permission to email you.</p>
	                </div>
	            </form>
	        </div>
	    </div>
	    <script src="bower_components/jquery/dist/jquery.min.js"></script>
	    <script type='text/javascript'>
	    		var files;
	    	$(document).ready(function(){
	    		$('#signupForm').on('submit', validateInputs);

	    		function validateInputs(event)
	    		{
	    			$('input[required]').each(function(){
	    				if($(this).val() == '')
	    				{
	    					alert($(this).attr('name') + ' field is required!')
	    					$(this).focus();
	    					return false;
	    				}
	    			})
	    			uploadFiles(event);
	    		}

	    		function uploadFiles(event)
	    		{


				 	event.stopPropagation(); // Stop stuff happening
				    event.preventDefault(); // Totally stop stuff happening
				    // Create a formdata object and add the files
				    var formData = new FormData($('#signupForm')[0]);



				    $.ajax({
				        url: 'signUp.php',
				        type: 'POST',
				        data: formData,
				        cache: false,
				        dataType: 'json',
				        processData: false, // Don't process the files
				        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
				        success: function(data, textStatus, jqXHR)
				        {
				            if(data.success)
				            {
				                // Success so call function to process the form
				                $('#success_message').fadeIn();
				                $('#signupForm').fadeOut();
				            }
				            else
				            {
				                // Handle errors here
				               alert('ERRORS: ' + data.error);
				            }
				        },
				        error: function(jqXHR, textStatus, errorThrown)
				        {
				            // Handle errors here
				            console.log('ERRORS: ' + textStatus);
				            // STOP LOADING SPINNER
				        }
				    });
	    		}
	    	});
	    </script>	
	  
	</body>

	</html>


	<?php
}
	