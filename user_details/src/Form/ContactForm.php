<?php
/**
 * @file
 * Contains \Drupal\amazing_forms\Form\PaymentForm.
 */

namespace Drupal\user_details\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\user\Entity\User;
use Drupal\std_hacks\Controller\APIController;
use Drupal\Core\Url;

/**
 * Payment form.
 */
class ContactForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
	   
    return 'user_details_contact_form';
  
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	$query = db_select('custom_country','cc');
    $query->fields('cc', ['id', 'country_name','description','country_code','phone_code']);
    $query->condition('status','1');
    $result = $query->execute();
    $data = $result->fetchAll();
    $cntry_code = array();
    foreach ($data as $value) {
  		$cntry_code[$value->id] = $value->country_code;
        $phone[$value->phone_code] = $value->phone_code;
    }
    
    $user_uid = \Drupal::currentUser()->id();
	$query = db_select('custom_users_information','cui');
    $query->fields('cui', ['id', 'contact_email']);
    $query->condition('id', $user_uid);
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
    }
    
	$userquery = db_select('custom_users','cu');
    $userquery->fields('cu', ['id', 'country_code', 'phone_number', 'email','role']);
    $userquery->condition('id', $user_uid);
    $userresult = $userquery->execute();
    $userdata = $userresult->fetchAll();
    foreach ($userdata as $uservalue) {
    }
    //$userrole=$userdata[]['role'];
    $query1 = db_select('custom_users_information','cui');
    $query1->fields('cui');
    $query1->condition('user_id', $user_uid);
    $result1 = $query1->execute();
    $data1 = $result1->fetchObject();
    $country_user=$data1->country;
    $form['#attached']['library'][] = 'user_details/user-details';
    
    $form['Phone_number'] = array(
	  '#type' => 'fieldset',
	  '#title' => t('How can we contact you?'),
	);
    
  if($uservalue->country_code=="" && $country_user!="")
    {
    	
    	$query1 = db_select('custom_country','cc');
    	$query1->fields('cc');
    	$query1->condition('id', $country_user);
    	$result1 = $query1->execute();
    	$data1 = $result1->fetchObject();
    	$country_code=$data1->phone_code;
    	$form['Phone_number']['field_country_code'] = array(
    			'#type' => 'select',
    			'#title' => t('Country Code'),
    			'#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
    			'#suffix' => '</div>',
    			'#empty_option' => $this->t('Select'),
    			'#options' => $phone,
    			'#default_value' =>$country_code,
    			'#required' => TRUE,
    	);
    	
    }
    else
    {
    	$form['Phone_number']['field_country_code'] = array(
    			'#type' => 'select',
    			'#title' => t('Country Code'),
    			'#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
    			'#suffix' => '</div>',
    			'#empty_option' => $this->t('Select'),
    			'#options' => $phone,
    			'#default_value' =>$uservalue->country_code,
    			'#required' => TRUE,
    	);
    }
   
    $form['Phone_number']['field_phone_number'] = array(
      '#type' => 'textfield',
      '#title' => t('Mobile Number'),
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>', 
      '#default_value' =>$uservalue->phone_number,
      '#required' => TRUE,
      '#attributes' => array(
    		'title' => t('The country code has included "0". If your mobile number is +60 123 4567, you should key in only 123 4567'),
    		'data-toggle'=>'tooltip'
      )
    );
    $form['field_contact_email'] = array(
      '#type' => 'textfield',
      '#title' => t('Email Address'),
      '#disabled' => TRUE,
      '#default_value' =>$uservalue->email,
      '#required' => TRUE,
    );
    $userrole = \Drupal::currentUser()->getRoles();
    $form['markup_mandatory'] = array('#markup' => "<p class='text-danger'> * denotes mandatory fields </p>");
    $form['back'] = array(
    		'#type' => 'markup',
    		'#markup' => '<span onClick="history.go(-1); return false;" class="btn btn-default w-150 m-sm-r m-md-b back-button">Back</span>',
    );
    if($userrole[1]=="guest")
    {
	   $form['next'] = array(
		  '#type' => 'submit',
		  '#value' => t('Save'),
		  '#submit' => array('::newSubmissionHandlerNext'),
		  '#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r m-md-b")),
		);
    }
    else 
    {
    	$form['next'] = array(
    			'#type' => 'submit',
    			'#value' => t('Next'),
    			'#submit' => array('::newSubmissionHandlerNext'),
    			'#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r m-md-b")),
    	);
    }
  /* $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
      '#attributes' => array("class"=>array("btn","btn-default","w-150","m-sm-r")),
    );*/
     $form['back'] = array(
	  '#type' => 'markup',
	  '#markup' => '<span onClick="history.go(-1); return false;" class="btn btn-default w-150 m-sm-r back-button"><i class="fa fa-angle-left fa-fw"></i>Back</span>',
	);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
	 $selected_country = $form_state->getValue('field_country_code');
	 $selected_number = $form_state->getValue('field_phone_number');
     $numberlength = strlen($selected_number);
     if (!is_numeric($selected_number))
		{
			$form_state->setErrorByName('field_phone_number', t('That is not a valid phone number.'));
		}
     /*Malasiya*/
	 if($selected_country == '+60'){
		 if(($numberlength < 8) || ($numberlength > 11)){
			 $form_state->setErrorByName('field_phone_number', t('Please enter valid number(Minimum digit: 8 or maximum digit: 11).'));
		 }
	 }
	 
	 /*Indonesia*/
	 if($selected_country == '+62'){
		 if(($numberlength < 9) || ($numberlength > 12)){
			 $form_state->setErrorByName('field_phone_number', t('Please enter valid number(Minimum digit: 9 or maximum digit: 12).'));
		 }
	 }
	 
	 /*Singapore*/
	 if($selected_country == '+65'){
		 if(($numberlength < 8) || ($numberlength > 11)){
			 $form_state->setErrorByName('field_phone_number', t('Please enter valid number(Minimum digit: 8 or maximum digit: 11).'));
		 }
	 }
	 
	 /*Thailand*/
	 if($selected_country == '+66'){
		 if(($numberlength < 8) || ($numberlength > 11)){
			 $form_state->setErrorByName('field_phone_number', t('Please enter valid number(Minimum digit: 8 or maximum digit: 11).'));
		 }
	 }
	 
	 /* Philippines*/
	 if($selected_country == '+63'){
		if(($numberlength < 9) || ($numberlength > 12)){
			 $form_state->setErrorByName('field_phone_number', t('Please enter valid number(Minimum digit: 8 or maximum digit: 12).'));
		 }
	 }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
  	$user_contact_uid = \Drupal::currentUser()->id();
  	$email = $form_state->getValue('field_contact_email');
  	$country_code = $form_state->getValue('field_country_code');
  	$phone_number = $form_state->getValue('field_phone_number');
  	
  	$inputarrays = array("uid"=>$user_contact_uid,"email"=>$email,"country_code"=>$country_code,"phone_number"=>$phone_number);
  	
  	$classobj = new APIController();
  	
  	$from = 'Ezplor';
  	$to = $form_state->getValue('field_country_code').$form_state->getValue('field_phone_number');
  	//$msg = rand();
  	$to=ltrim($to, '+0');
  	//$msg="Your%20NIDA%20Explore%20verification%code%is%20$otp";
  	$otp = rand(pow(10, 4-1), pow(10, 4)-1);
  	$msg=urlencode("Your Ezplor verification code is ".$otp);
  	
  	
  	$inputarray = array("from"=>$from,"to"=>$to,"msg"=>$msg,"country_code"=>$country_code);
  	
  	$urlcount = db_select('custom_users')
  	->condition('id', $user_contact_uid)
  	->condition('phone_number', $phone_number)
  	->countQuery()->execute()->fetchField();
  	//if ($urlcount != 0) {
  	$query_status = db_select('custom_users','cu');
  	$query_status->fields('cu',['phone_verify_status','country_code','phone_number']);
  	$query_status->condition('id', $user_contact_uid);
  	$result_status = $query_status->execute();
  	$data_status = $result_status->fetchAll();
  	$phone_status=$data_status[0]->phone_verify_status;
  	$user_code=$data_status[0]->country_code;
  	$user_phone=$data_status[0]->phone_number;
  	if($phone_status=="1" && $country_code==$user_code && $phone_number==$user_phone){
  		
  		//Update profile contact status to 1 in custom users information table
  		$query = \Drupal::database()->update('custom_users_information');
  		$query->fields(['contact_status' => 1]);
  		$query->condition('id', $user_contact_uid);
  		$query->execute();
  		$userrole = \Drupal::currentUser()->getRoles();
  		if($userrole[1]=="guest")
  		{
  			$redirect_path_next = "/profile/thank-you";
  			$url_next = url::fromUserInput($redirect_path_next);
  			$form_state->setRedirectUrl($url_next);
  		}
  		else
  		{
  			// set relative internal path
  			$redirect_path = "/profile/payment";
  			$url = url::fromUserInput($redirect_path);
  			$form_state->setRedirectUrl($url);
  		}
  		
  	}
  	else{
  		
  		//$result = $classobj->global_sms_trigger($inputarray);
  		// set relative internal paths
  		$redirect_path = "/profile/otpcode";
  		$url = url::fromUserInput($redirect_path);
  		
  		$count = db_select('custom_otp_verification')
  		->condition('user_id', $user_contact_uid)
  		->countQuery()->execute()->fetchField();
  		if ($count != 0) {
  			\Drupal::database()->update('custom_otp_verification')
  			->condition('user_id' , $user_contact_uid)
  			->fields([
  					'country_code' => $country_code,
  					'phone_number' => $phone_number,
  					'otp_code' => $otp,
  			])
  			->execute();
  		}
  		else{
  			$query = \Drupal::database()->insert('custom_otp_verification');
  			$query->fields([
  					'user_id',
  					'country_code',
  					'phone_number',
  					'otp_code',
  			]);
  			$query->values([
  					$user_contact_uid,
  					$country_code,
  					$phone_number,
  					$otp,
  			]);
  			$query->execute();
  			
  		}
  		
  		
  		//drupal_set_message("");
  		// set redirect
  		$form_state->setRedirectUrl($url);
  	}
 
	
    
   }
   
       /**
   * Custom submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function newSubmissionHandlerNext(array &$form, FormStateInterface $form_state) {
      $user_contact_uid = \Drupal::currentUser()->id();
      $email = $form_state->getValue('field_contact_email');
	  $country_code = $form_state->getValue('field_country_code');
	  $phone_number = $form_state->getValue('field_phone_number');
		
	  $inputarrays = array("uid"=>$user_contact_uid,"email"=>$email,"country_code"=>$country_code,"phone_number"=>$phone_number);

      $classobj = new APIController();
      
        $from = 'Ezplor';
		$to = $form_state->getValue('field_country_code').$form_state->getValue('field_phone_number');
		//$msg = rand();
		$to=ltrim($to, '+0');
		//$msg="Your%20NIDA%20Explore%20verification%code%is%20$otp";
		$otp = rand(pow(10, 4-1), pow(10, 4)-1);
		$msg=urlencode("Your Ezplor verification code is ".$otp);
		
     
		$inputarray = array("from"=>$from,"to"=>$to,"msg"=>$msg,"country_code"=>$country_code);
      
      $urlcount = db_select('custom_users')
				->condition('id', $user_contact_uid)
				->condition('phone_number', $phone_number)
				->countQuery()->execute()->fetchField();
				//if ($urlcount != 0) {
				$query_status = db_select('custom_users','cu');
				$query_status->fields('cu',['phone_verify_status','country_code','phone_number']);
				$query_status->condition('id', $user_contact_uid);
				$result_status = $query_status->execute();
				$data_status = $result_status->fetchAll();
				$phone_status=$data_status[0]->phone_verify_status;
				$user_code=$data_status[0]->country_code;
				$user_phone=$data_status[0]->phone_number;
				if($phone_status=="1" && $country_code==$user_code && $phone_number==$user_phone){
					//Update profile contact status to 1 in custom users information table
					$query = \Drupal::database()->update('custom_users_information');
					$query->fields(['contact_status' => 1]);
					$query->condition('id', $user_contact_uid);
					$query->execute();
					$userrole = \Drupal::currentUser()->getRoles();
					if($userrole[1]=="guest")
					{
					   $redirect_path_next = "/profile/thank-you";
						$url_next = url::fromUserInput($redirect_path_next);
						$form_state->setRedirectUrl($url_next);
					 }
					 else
					 {
					 	// set relative internal path
					 	$redirect_path = "/profile/payment";
					 	$url = url::fromUserInput($redirect_path);
					 	$form_state->setRedirectUrl($url);
					 }
					
				}
				else{
					
					//$result = $classobj->global_sms_trigger($inputarray);
					// set relative internal path
						$redirect_path = "/profile/otpcode";
						$url = url::fromUserInput($redirect_path);
						
						$count = db_select('custom_otp_verification')
							->condition('user_id', $user_contact_uid)
							->countQuery()->execute()->fetchField();
							if ($count != 0) { 
								\Drupal::database()->update('custom_otp_verification')
									->condition('user_id' , $user_contact_uid)
									->fields([
										'country_code' => $country_code,
										'phone_number' => $phone_number,
										'otp_code' => $otp,
									])
									->execute();
							}
							else{
								$query = \Drupal::database()->insert('custom_otp_verification');
								$query->fields([
								  'user_id',
								  'country_code',
								  'phone_number',
								  'otp_code',
								]);
								$query->values([
									$user_contact_uid,
									$country_code,
									$phone_number,
									$otp,
								]);
								$query->execute();

							}	
								
							
						//drupal_set_message("");
						// set redirect
						$form_state->setRedirectUrl($url);
				}
    }   
}
?>
