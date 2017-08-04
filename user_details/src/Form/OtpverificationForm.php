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
class OtpverificationForm extends FormBase {
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
    //get users phone number
    $user_contact_uid = \Drupal::currentUser()->id();
    $query = db_select('custom_otp_verification','cov');
    $query->fields('cov', ['id', 'user_id','country_code','phone_number','otp_code']);
    $query->condition('user_id', $user_contact_uid);
    $result = $query->execute();
    $data = $result->fetchAll();
    $user_code=$data[0]->country_code;
    $user_phone=$data[0]->phone_number;
    
    
    
    $form['phone_number']= array(
        '#title' =>  t('phonenumber'),
        
        '#value' => $user_code . $user_phone,
        
        '#type' => 'hidden',
        
    );
    
    $form['firebase_credential']= array(
        '#title' =>  t('credential'),        
        '#type' => 'hidden',
        
    );
    $form['text'] = array(
    		'#type' => 'markup',
    		'#markup' => t(' Please click on "Request OTP Code" to receive your 6-digit OTP. <br/><br/>'),
    		'#prefix' => '<div id="otp_message">',
    		'#suffix' => '</div>',
    );
   
    
    $form['otp'] = array(
	  '#type' => 'fieldset',
	);
    $form['otp_code'] = array(
      '#type' => 'textfield',
      '#title' => t('Enter your otp code'),
      '#required' => TRUE,
    );
    
    
    $form['#attached']['library'][] = 'user_details/user-details-firebase';
   
    $form['dummy'] = array(
        '#type' => 'button',
        '#value' => t('Request-OTP Code'),
        '#attributes' => array("class"=>array(" btn btn-danger")),
    );
   
   $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Submit'),
      '#submt' => array("::newsubmitForm"),
      '#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r"), "style" => array(" display:none")),
    );
   
   $form['submit1'] = array(
       '#type' => 'submit',
       '#value' => t('Submit'),
       '#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r"), "style" => array(" display:none")),
   );
   
   $form['back'] = array(
   		'#type' => 'markup',
   		'#markup' => '<span onClick="history.go(-1); return false;" class="btn btn-default w-150 m-sm-r back-button"><i class="fa fa-pencil fa-fw"></i>Edit Number</span>',
   );
   
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
	$user_contact_uid = \Drupal::currentUser()->id();
	//$otp_code = $form_state->getValue('otp_code');
	
	$query = db_select('custom_otp_verification','cov');
    $query->fields('cov', ['id', 'user_id','country_code','phone_number','otp_code']);
    $query->condition('user_id', $user_contact_uid);
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
    }
    $userquery = db_select('custom_users','cu');
    $userquery->fields('cu', ['id', 'country_code', 'phone_number', 'email']);
    $userquery->condition('id', $user_contact_uid);
    $userresult = $userquery->execute();
    $userdata = $userresult->fetchAll();
    foreach ($userdata as $uservalue) {
    }
    
    $email = $uservalue->email;;
	$country_code = $value->country_code;
	$phone_number = $value->phone_number;
	
	$inputarray = array("uid"=>$user_contact_uid,"email"=>$email,"country_code"=>$country_code,"phone_number"=>$phone_number,"phone_verify_status"=>1);
	$classobj = new APIController();
    $result = $classobj->contact_details_update($inputarray);
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
	     if($result == 1){

          drupal_set_message('Your Contact Details Completed Successfully.');

      } else {

         drupal_set_message($result);
      }
      
      drupal_set_message('Your Contact Details Completed Successfully.');

  }
  
  public function newsubmitForm(array &$form, FormStateInterface $form_state) {
      $user_contact_uid = \Drupal::currentUser()->id();
      //$otp_code = $form_state->getValue('otp_code');
      
      $query = db_select('custom_otp_verification','cov');
      $query->fields('cov', ['id', 'user_id','country_code','phone_number','otp_code']);
      $query->condition('user_id', $user_contact_uid);
      $result = $query->execute();
      $data = $result->fetchAll();
      foreach ($data as $value) {
      }
      $userquery = db_select('custom_users','cu');
      $userquery->fields('cu', ['id', 'country_code', 'phone_number', 'email']);
      $userquery->condition('id', $user_contact_uid);
      $userresult = $userquery->execute();
      $userdata = $userresult->fetchAll();
      foreach ($userdata as $uservalue) {
      }
      
      $email = $uservalue->email;;
      $country_code = $value->country_code;
      $phone_number = $value->phone_number;
      
      $inputarray = array("uid"=>$user_contact_uid,"email"=>$email,"country_code"=>$country_code,"phone_number"=>$phone_number,"phone_verify_status"=>1);
      $classobj = new APIController();
      $result = $classobj->contact_details_update($inputarray);
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
      if($result == 1){
          
          drupal_set_message('Your contact details are successfully updated.');
          
      } else {
          
          drupal_set_message($result);
      }
      
  }
  
  
}
?>
