// Initialize Firebase
  var config = {
    apiKey: "AIzaSyBUXtmpp4VqlONaEeeWIdvLZ0SCFIu3UA4",
    authDomain: "ezplor-5b21b.firebaseapp.com",
    databaseURL: "https://ezplor-5b21b.firebaseio.com",
    projectId: "ezplor-5b21b",
    storageBucket: "ezplor-5b21b.appspot.com",
    messagingSenderId: "283862311728"
  };
  firebase.initializeApp(config);

window.onload = function() {
    
	document.getElementById('edit-submit').addEventListener('click', onVerifyCodeSubmit);
	document.getElementById('edit-submit').style.display = 'none';
	
    // [START appVerifier]
    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('edit-dummy', {
      'size': 'invisible',
      'callback': function(response) {
        // reCAPTCHA solved, allow signInWithPhoneNumber.
        onSignInSubmit();
      }
    });
    // [END appVerifier]
    recaptchaVerifier.render().then(function(widgetId) {
      window.recaptchaWidgetId = widgetId;
    });
  };
  /**
   * Function called when clicking the Login/Logout button.
   */
  function onSignInSubmit() {
      window.signingIn = true;
      var phoneNumber = document.getElementsByName("phone_number")[0].value;
      var appVerifier = window.recaptchaVerifier;
      firebase.auth().signInWithPhoneNumber(phoneNumber, appVerifier)
          .then(function (confirmationResult) {
            // SMS sent. Prompt user to type the code from the message, then sign the
            // user in with confirmationResult.confirm(code).
            window.confirmationResult = confirmationResult;
            window.signingIn = false;
            document.getElementById('edit-submit').style.display = '';
            document.getElementById('edit-dummy').style.display = 'none';
            document.getElementById('otp_message').style.display = 'none';
            var maskedNumber = phoneNumber.substr(0, 5) + "xxxx"+ phoneNumber.substr(9); 
            var message = "A verification code has been sent to your mobile number "+ maskedNumber + ".Please key the code and click Submit!"
            window.alert(message);
            
          }).catch(function (error) {
            // Error; SMS not sent
            console.error('Error during signInWithPhoneNumber', error);
            window.alert('Error during signInWithPhoneNumber:\n\n'
                + error.code + '\n\n' + error.message);
            window.signingIn = false;
          });
    
  }
  /**
   * Function called when clicking the "Verify Code" button.
   */
  function onVerifyCodeSubmit(e) {
	  var event = e;
	e.preventDefault();
	
    if (!!getCodeFromUserInput()) {
    	
      window.verifyingCode = true;
      var code = getCodeFromUserInput();
      confirmationResult.confirm(code).then(function (result) {
        // User signed in successfully.
        var user = result.user;
        window.verifyingCode = false;
        window.confirmationResult = null;
        //submit form;
        document.getElementById('edit-submit1').click();
        
      }).catch(function (error) {
        // User couldn't sign in (bad verification code?)
    	  event.preventDefault();
    	  e.preventDefault();
        console.error('Error while checking the verification code', error);
        window.alert(error.code);
        window.verifyingCode = false;
        
      });
    }
    else{
    	e.preventDefault();
    }
    //return false;
  }
  
  function getCodeFromUserInput() {
	    return document.getElementById('edit-otp-code').value;
	  }
  
  
