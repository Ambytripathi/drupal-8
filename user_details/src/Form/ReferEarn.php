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
class ReferEarn extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
	  
    return 'user_details_refer_form';
	  
  
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	  // $uid = \Drupal::currentUser()->id();
   //  $query = db_select('custom_users','user');
   //  $query->fields('user', ['role']);
   //  $query->condition('id', $uid, '');
   //  $result = $query->execute();
   //  $data = $result->fetchAssoc();
    
   //  print_r($data); exit;
  	$user_payment_uid = \Drupal::currentUser()->id();
   
  	$query = db_select('custom_users','c');
  	$query->fields('c');
  	$query->condition('id', $user_payment_uid);
  	$result = $query->execute();
  	$data = $result->fetchObject();
  	$role=$data->role;
  	$query1 = db_select('custom_users_information','cu');
  	$query1->fields('cu');
  	$query1->condition('id', $user_payment_uid);
  	$result1 = $query1->execute();
  	$data1 = $result1->fetchObject();
  	
  	$introduce_status=$data1->introduce_status;
  	$contact_status=$data1->contact_status;
  	$location_status=$data1->location_status;
  	$payment_status=$data1->payment_status;
  	$refer_status=$data1->refer_management_approval;
  	//check if all status complete
  	$msg="";
  	
  		 if($introduce_status=="1" && $contact_status=="1" && $location_status=="1" && $payment_status=="1" && $refer_status=="1")
  		 {
  			$roles = \Drupal::currentUser()->getRoles();
  			
  			
  			$form['#attached']['library'][] = 'user_details/user-details';
  			
  			$form['refer_promoters'] = array(
  					'#type' => 'fieldset',
  					'#title' => t('Refer & Earn:'),
  			);
  			
  			
  			$form['refer_promoters']['email'] = array(
  					'#type' => 'email',
  					'#title' => t('Email Address:'),
  					'#attributes' => array('id' => 'InsertRecordPICKUP_CITY'),
            '#required' => TRUE,
  			);
  			
  			if($roles[1]=='partners'){
  				$form['refer_promoters']['role'] = array(
  						'#type' => 'hidden',
  						'#title' => t('Role:'),
  						'#default_value' => 'partners',
  				);
  				// $form['refer_promoters']['active'] = array(
  				//   '#type' => 'radios',
  				//   '#title' => $this->t('Role'),
  				//   '#default_value' => 'partners',
  				//   '#options' => array('partners' => $this->t('Promoters')),
  				//  );
  			}else if($roles[1]=='diplomats'){
  				$form['refer_promoters']['role'] = array(
  						'#type' => 'radios',
  						'#title' => $this->t('Role'),
  						'#default_value' => 'local_experts',
  						'#options' => array('local_experts' => $this->t('Local Experts')),
  				);
          // $form['refer_promoters']['role'] = array(
          //     '#type' => 'radios',
          //     '#title' => $this->t('Role'),
          //     '#default_value' => 'local_experts',
          //     '#options' => array('local_experts' => $this->t('Local Experts'), 'partners' => $this->t('Promoters')),
          // );
  			}else if($roles[1]=='guest'){
  				$form['refer_promoters']['role'] = array(
  						'#type' => 'hidden',
  						'#title' => t('Role:'),
  						'#default_value' => 'guest',
  				);
  			}
  			
  			
  			// $form['refer_promoters']['role'] = array(
  			//   '#type' => 'radios',
  			//   '#title' => $this->t('Diplomats'),
  			//   '#default_value' => 'diplomats',
  			//   '#options' => array(0 => $this->t('Active')),
  			// );
  			// $form['refer_promoters']['role'] = array(
  			//   '#type' => 'radios',
  			//   '#title' => $this->t('Promoters'),
  			//   '#default_value' => 'promoters',
  			// );
  			
  			/***********************************************************************/
  			
  			
  			
  			/*$form['next'] = array(
  			 '#type' => 'submit',
  			 '#value' => t('Next'),
  			 '#submit' => array('::newSubmissionHandlerNext'),
  			 '#attributes' => array("class"=>array(" btn btn-primary w-150 m-sm-r")),
  			 );
  			 $form['back'] = array(
  			 '#type' => 'markup',
  			 '#markup' => '<span onClick="history.go(-1); return false;" class="btn btn-danger w-150 m-sm-r back-button">Back</span>',
  			 );
  			
  			 */
  			$form['markup_mandatory'] = array('#markup' => "<p class='text-danger'> * denotes mandatory fields </p>");
  			$form['submit'] = array(
  					'#type' => 'submit',
  					'#value' => t('Send'),
  					'#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r")),
  			);
  			
  		 }
  		 elseif($introduce_status=="1" && $contact_status=="1" && $location_status=="1" && $payment_status=="1" && $refer_status=="0")
  		 {
  		 	drupal_set_message($msg. "Please wait for Ezplor Team to verify your profile" ,'success');
  		 }
  		 elseif($introduce_status=="1" && $contact_status=="1" && $location_status=="1" && $payment_status=="1" && $refer_status=="3")
  		 {
  		 	drupal_set_message($msg. "Your refer and earn is deactivated.Please contact Ezplor Team." ,'error');
  		 }
  		 elseif($introduce_status=="1" && $contact_status=="1" && $location_status=="1" && $payment_status=="1" && $refer_status=="2")
  		 {
  		 	drupal_set_message($msg. "Sorry!Your Profile is rejected by Ezplor Team.So you cannot access Refer & Earn" ,'error');
  		 }
  		 else
  		 {
  		 	$form=NULL;
  		 	if($introduce_status=="0" )
  		 	{
  		 		if($msg=="")
  		 		{
  		 			$msg=$msg. "Please fill in Introduce Yourself Section";
  		 		}
  				
  		 	}
  		 	if($contact_status=="0" )
  		 	{
  		 		if($msg=="")
  		 		{
  		 			$msg=$msg. "Please fill in Contact Details";
  		 		}
  		 		else
  		 		{
  		 			$msg=$msg. ", Contact Details ";
  		 		}
  		
  		 	}
  		 	if($location_status=="0" )
  		 	{
  		 		if($msg=="")
  		 		{
  		 			$msg=$msg. "Please fill in Location Details";
  		 		}
  		 		else
  		 		{
  		 			$msg=$msg. ", Location Details ";
  		 		}
  				
  		 	}
  		 	if($payment_status=="0" )
  		 	{
  		 		if($msg=="")
  		 		{
  		 			$msg=$msg. "Please fill in Payment Details";
  		 		}
  		 		else
  		 		{
  		 			$msg=$msg. "& Payment Details ";
  		 		}
  				
  				
  		 	}
  		 	drupal_set_message($msg. " To complete your profile." ,'warning');
  			
  		 }
  	


  	
  	
  	
  	
  	
  	
  	
  	
 
   //$form=null;
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
	 
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	global $base_url;
    $uid = \Drupal::currentUser()->id();
    $role = $form_state->getValue('role');

    // Check email id in referal
    $ref_query = db_select('custom_referral','c');
    $ref_query->fields('c',['email_id', 'user_id']);
    $ref_query->condition('email_id', $form_state->getValue('email'));
    $ref_result = $ref_query->execute();
    $ref_data = $ref_result->fetchObject();
    $ref_email=$ref_data->email_id;
    $ref_user_id = $ref_data->user_id;

    if(!empty($ref_email) && $ref_user_id!=$uid){
      drupal_set_message("Email id: ".$form_state->getValue('email')." This person already referred.", 'warning');
      return false;
    }

    if(!empty($ref_email) && $ref_user_id==$uid){
      drupal_set_message("Email id: ".$form_state->getValue('email')." You referred this person already.", 'warning');
      return false;
    }
     

    // Check email id in user
    $user_query = db_select('custom_users','c');
    $user_query->fields('c',['email']);
    $user_query->condition('email', $form_state->getValue('email'));
    $user_result = $user_query->execute();
    $user_data = $user_result->fetchObject();
    $user_email=$user_data->email;

    if(!empty($user_email)){
      drupal_set_message("Email id: ".$form_state->getValue('email')." already registered with us.", 'warning');
      return false;
    }


    if(!empty($role)){
        $post = array(
          'email_id' => $form_state->getValue('email'),
          'type' => $form_state->getValue('role'),
          'status' => 'Pending',
          'user_id' => $uid,
        );
    }else{
        $post = array(
          'email_id' => $form_state->getValue('email'),
          'type' => 'Pending',
          'user_id' => $uid,
        );
    }
    
    $insert = db_insert('custom_referral')
    -> fields($post)
    ->execute();

    if($insert>0){
    	$mail = $form_state->getValue('email');
    	$role= $form_state->getValue('role');
    	if($role=="partners")
    	{
    		$msg="<html lang='en'><head><title> Ezplor </title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'> </head><body style='background: #ddd; min-height:1000px;height:auto; padding-top:10px; padding-bottom:10px; font-family: Arial,Helvetica Neue,Helvetica,sans-serif;'><div style='width:600px;margin-left: auto; margin-right: auto;padding-left: 15px;padding-right: 15px;background: #fff;'><div style='float: left;width:100%;background: #fff;'><div style='float:left;width:100%;'><div style='float:left;width:100%;min-height:100px;text-align: center;''><div ><img style='text-align:center;width:175px;min-height:75px;'src='$base_url/themes/explore/images/logo.png'></div></div></div><div style='float:left;width:100%;width:600px;height: 200px;'><img src='$base_url/themes/explore/images/header.jpg' /></div><div style='float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: justify; margin:40px; line-height: 24px;font-family: Arial,Helvetica Neue,Helvetica,sans-serif; '>Hi there, <br /><br />This is an exclusive invitation for you to be a Promoter under the Ezplor team. As a Promoter, you will earn comission for every successful booking made.  Read more about it <a href='$base_url/partners'> here. </a><br><br>Ezplor is a marketplace that connects independent people who are looking for extra income with travelers seeking to experience a country through a native’s perspective. Embark on an extraordinary journey with us by being part of the team!<br><br>Click onto the link below to take the first step to be part of the Ezplor family.<br><a href='$base_url/partners'>$base_url/partners</a><br><br>Sincerely, <br> Ezplor Team<br></p></div><div style='float:left;width:100%; text-align: center;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px 0;'>Follow us:</p><a href='https://business.facebook.com/Ezplor-279099575892999/' target='_blank'><img src='$base_url/themes/explore/images/facebook.png'/></a><a href='https://www.instagram.com/ezplor/' target='_blank'><img src='$base_url/themes/explore/images/insta.png'/></a><a href='https://twitter.com/ezplor' target='_blank'><img src='$base_url/themes/explore/images/tweeter.png'/></a></div><div style='background: #5bb85d;color:#fff;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 30px;margin:20px 0;'>© 2017 Ezplor. All Rights Reserved.</p></div></div></div></body></html>";
    		//$msg="<html><body><div id='emailcontents' style='min-height:100px; padding:10px; padding-bottom:0px;'>Hi,<br /><br /><p > This is an exclusive invitation for you to be a Promoter under the Ezplor team. As a Promoter, you will earn comission for every successful booking made.  Read more about it <a href='$base_url/partners'> here. </a><br><br>Ezplor is a marketplace that connects independent people who are looking for extra income with travelers seeking to experience a country through a native’s perspective. Embark on an extraordinary journey with us by being part of the team!<br><br>Click onto the link below to take the first step to be part of the Ezplor family.<br><a href='$base_url/expert/register'>$base_url/expert/register</a></p><p >Sincerely, <br><a  style='text-decoration:none; font-family:Arial, Helvetica, sans-serif ;color:#555555; text-decoration:none; line-height:18px; display:block; float:left;font-size:12px; z-index:100 !important; font-weight:bold;' >Ezplor Team</a><div class='clr' style='clear:both;'></div></div></body></html>";
    	}
    	
    	elseif($role=="local_experts")
    	{
    		$msg="<html lang='en'><head><title> Ezplor </title><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'> </head><body style='background: #ddd; min-height:1000px;height:auto; padding-top:10px; padding-bottom:10px; font-family: Arial,Helvetica Neue,Helvetica,sans-serif;'><div style='width:600px;margin-left: auto; margin-right: auto;padding-left: 15px;padding-right: 15px;background: #fff;'><div style='float: left;width:100%;background: #fff;'><div style='float:left;width:100%;'><div style='float:left;width:100%;min-height:100px;text-align: center;''><div ><img style='text-align:center;width:175px;min-height:75px;'src='$base_url/themes/explore/images/logo.png'></div></div></div><div style='float:left;width:100%;width:600px;height: 200px;'><img src='$base_url/themes/explore/images/header.jpg' /></div><div style='float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: justify; margin:40px; line-height: 24px;font-family: Arial,Helvetica Neue,Helvetica,sans-serif; '>Hi there, <br /><br />This is an exclusive invitation for you to be a Local Expert under the Ezplor team. As a Local Expert, you can create your experiences and earn money for every successful booking made for your offer. Read more about it <a href='$base_url/localexperts'> here. </a><br><br>Ezplor is a marketplace that connects independent people who are looking for extra income with travelers seeking to experience a country through a native’s perspective. Embark on an extraordinary journey with us by being part of the team!<br><br>Click onto the link below to take the first step to be part of the Ezplor family.<br><a href='$base_url/localexperts'>$base_url/localexperts/</a><br><br>Sincerely, <br> Ezplor Team<br></p></div><div style='float:left;width:100%; text-align: center;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px 0;'>Follow us:</p><a href='https://business.facebook.com/Ezplor-279099575892999/' target='_blank'><img src='$base_url/themes/explore/images/facebook.png'/></a><a href='https://www.instagram.com/ezplor/' target='_blank'><img src='$base_url/themes/explore/images/insta.png'/></a><a href='https://twitter.com/ezplor' target='_blank'><img src='$base_url/themes/explore/images/tweeter.png'/></a></div><div style='background: #5bb85d;color:#fff;float:left;width:100%;'><p style='font-family: Arial,Helvetica Neue,Helvetica,sans-serif;font-size:16px;text-align: center;margin:20px; line-height: 30px;margin:20px 0;'>© 2017 Ezplor. All Rights Reserved.</p></div></div></div></body></html>";
    		//$msg="<html><body><div id='emailcontents' style='min-height:100px; padding:10px; padding-bottom:0px;'>Hi,<br /><br /><p > This is an exclusive invitation for you to be a Local Expert under the Ezplor team. Read more about it <a href='$base_url/localexperts'> here. </a><br><br>Ezplor is a marketplace that connects independent people who are looking for extra income with travelers seeking to experience a country through a native’s perspective. Embark on an extraordinary journey with us by being part of the team!<br><br>Click onto the link below to take the first step to be part of the Ezplor family.<br><a href='$base_url/expert/register'>$base_url/expert/register</a></p><p >Sincerely, <br><a  style='text-decoration:none; font-family:Arial, Helvetica, sans-serif ;color:#555555; text-decoration:none; line-height:18px; display:block; float:left;font-size:12px; z-index:100 !important; font-weight:bold;' >Ezplor Team</a><div class='clr' style='clear:both;'></div></div></body></html>";
    		
    	}
    	$input = array("from"=>"welcome@ezplor.com","to"=>$mail,"subject"=>"Your exclusive invite to be part of Ezplor.","category"=>"Invitation","msg"=>$msg);
    	$mail_out = APIController::global_mail_trigger($input);
    	drupal_set_message("Your Invitation has been sent successfully.");
    }else{
    	drupal_set_message(t("Your Invitation has not been sent."), 'warning');
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
      $user_location_uid = \Drupal::currentUser()->id();
      $country = $form_state->getValue('field_country');
	  //~ $city = $form_state->getValue('field_city');
	  //~ $address1 = $form_state->getValue('field_address1');
	  //~ $address2 = $form_state->getValue('field_address2');
	  //~ $state = $form_state->getValue('field_state');
	  //~ $postal_code = $form_state->getValue('field_postal_code');
	  $city = $form_state->getValue('city');
	  $address1 = $form_state->getValue('address');
	  $address2 = $form_state->getValue('field_address2');
	  $state = $form_state->getValue('state');
	  $postal_code = $form_state->getValue('zipcode');
	  
	  $details = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address1).'&sensor=false');
      $result = json_decode($details,true);
      $latitude=$result['results'][0]['geometry']['location']['lat'];
      $longitude=$result['results'][0]['geometry']['location']['lng'];
	  //~ $latitude = $form_state->getValue('field_latitude');
	  //~ $longitude = $form_state->getValue('field_longitude');
	  
      $inputarray = array("uid"=>$user_location_uid,"country"=>$country,"city"=>$city,"address1"=>$address1,"address2"=>$address2,"state"=>$state,"postal_code"=>$postal_code,"latitude"=>$latitude,"longitude"=>$longitude);

      $classobj = new APIController();

      $result = $classobj->location_details_update($inputarray);

      // set relative internal path
		$redirect_path_next = "/user/".$user_location_uid."/change-password";
		$url_next = url::fromUserInput($redirect_path_next);

	  // set redirect
	   $form_state->setRedirectUrl($url_next);
  }
}
?>
