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
use Drupal\Core\Mail\MailManagerInterface;
use Drupal\Component\Utility\SafeMarkup;
use Drupal\Component\Utility\Html;

/**
 * Payment form.
 */
class ReferPromoters extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
	   
    return 'user_details_payment_form';
  
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	
    $query = db_select('custom_roles','roles');
    $query->fields('roles', ['name', 'target_id']);
    $query->condition('target_id', array('administrator','management','editors'), 'NOT IN');
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
      $arrayName[$value->target_id]=t($value->name);

    }

  $form['#attached']['library'][] = 'user_details/user-details';
    
	$form['refer_promoters'] = array(
	  '#type' => 'fieldset',
	  '#title' => t('Refer Promoters:'),
	);
	
	
  $form['refer_promoters']['email'] = array(
    '#type' => 'textfield',
    '#title' => t('Email Address:'),
    '#attributes' => array('id' => 'InsertRecordPICKUP_CITY'),
  );

   $form['refer_promoters']['role'] = [
      '#type' => 'select',
      '#title' => $this->t('User Role'),
      '#options' => @$arrayName,
      '#empty_option' => $this->t('-Select User Role-'),
    ];
/***********************************************************************/    
	 
  

  $form['next'] = array(
	  '#type' => 'submit',
	  '#value' => t('Next'),
	  '#submit' => array('::newSubmissionHandlerNext'),
    '#attributes' => array("class"=>array(" btn btn-primary w-150 m-sm-r")),
	);
   $form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
      '#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r")),
    );
     $form['back'] = array(
	  '#type' => 'markup',
	  '#markup' => '<span onClick="history.go(-1); return false;" class="btn btn-danger w-150 m-sm-r back-button">Back</span>',
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
	global $base_url;
    $uid = \Drupal::currentUser()->id();
    $token_id = md5(uniqid(rand(), true));
    $password = 'nid@test'.rand(0, 99);
    $post = array(
        'email' => $form_state->getValue('email'),
        'username' => $form_state->getValue('email'),
        'role' => $form_state->getValue('role'),
        'status' => '0',
        'parent_id' => $uid,
        'token' => $token_id,
        'password' => md5($password),
      );
    $insert = db_insert('custom_users')
    -> fields($post)
    ->execute();



    if($insert>0){
    $mail = $form_state->getValue('email');
    $msg = "Your default password is $password. Please click the below link to activate<br/><a href='$base_url/activation?id=".$uid."&token=".$token_id."'>$base_url/activation?id=".$uid."&token=".$token_id."</a>";
    $input = array("from"=>"info@nidaexplore.com","to"=>$mail,"subject"=>"Registration","category"=>"Registration","msg"=>$msg);
    $mail_out = APIController::global_mail_trigger($input);
    drupal_set_message("Detail inserted successfully.");
    }else{
      drupal_set_message(t("Detail not inserted."), 'warning');
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
