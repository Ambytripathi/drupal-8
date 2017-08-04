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
class LocationForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
	   
    return 'user_details_location_form';
  
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $user_uid = \Drupal::currentUser()->id();
	  $query = db_select('custom_users_information','cui');
    $query->fields('cui', ['id', 'country', 'state', 'city', 'postal_code', 'latitude', 'longitude', 'address', 'address2']);
    $query->condition('id', $user_uid);
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
      if(isset($value->country)){ $country_id = $value->country; } else { $country_id = ''; }
      if(isset($value->city)){ $city_id = $value->city; } else { $city_id = ''; }
    }


    $query = db_select('custom_country','cc');
    $query->fields('cc', ['id', 'country_name','description','country_code']);
    $query->condition('status', 1);
    $query->orderBy('country_name', 'ASC');
    $result = $query->execute();
    $data = $result->fetchAll();
    $cntry_name = array();
    foreach ($data as $values) {
    $cntry_name[$values->id] = $values->country_name;
    }

    $query_result = db_select('custom_state','cc');
    $query_result->fields('cc', ["id","state_name"]);
    if($country_id > 0){ $query_result->condition('country_id',$country_id); }
    $result = $query_result->execute();
    $data = $result->fetchAll();
    foreach($data as $row){
        $sid = $row->id;
        $query = db_select('custom_city','cc');
        $query->fields('cc', ["id","city_name"]);
        $query->condition('state_id',$sid);
        $result = $query->execute();
        $dataer = $result->fetchAll();
        foreach ($dataer as $key => $values) {
            $city_array[$values->id] =$values->city_name;
        }

    }


    $form['#attached']['library'][] = 'user_details/user-details-location';
    
	$form['where_do_you_live'] = array(
	  '#type' => 'fieldset',
	  '#title' => t('Where do you live'),
	);
	$form['address'] = array(
	  '#type' => 'fieldset',
	  '#title' => t('Address'),
	);
	$form['where_do_you_live']['field_country'] = array(
      '#type' => 'select',
      '#empty_option' => $this->t('-Please Select Country Name-'),
      '#title' => t('Select country'),
      '#options' => $cntry_name,
      '#default_value' => $country_id,
	  '#required' => TRUE,
    );
    $form['where_do_you_live']['city'] = array(
      '#type' => 'select',
      '#empty_option' => $this->t('-Please Select City Name-'),
      '#title' => t('Select city'),
      '#options' => $city_array,
      '#validated' => TRUE,
      '#attributes' => array('id' => 'InsertRecordPICKUP_CITY'),
      '#default_value' => $city_id,
      '#required' => TRUE,
    );
 
/***********************************************************************/    
	$form['address']['address'] = array(
      '#type' => 'textfield',
      '#title' => t('Address Line 1:'),
      '#default_value' => $value->address,
      '#attributes' => array('id' => 'cbParamVirtual1'),
	  
    );
    $form['address']['field_address2'] = array(
      '#type' => 'textfield',
      '#title' => t('Address Line 2:'),
      '#default_value' => $value->address2,
        );
	$form['address']['state'] = array(
      '#type' => 'textfield',
      '#title' => t('State'),
      '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
      '#suffix' => '</div>', 
      '#default_value' => $value->state,
      '#attributes' => array('id' => 'InsertRecordPICKUP_STATE'),
	  '#required' => TRUE,
    );
    $form['address']['zipcode'] = array(
      '#type' => 'textfield',
      '#title' => t('Zip/post code'),
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div><div id="map" style="width:600px !important; height:400px !important;"></div>', 
      '#attributes' => array('id' => 'InsertRecordPICKUP_ZIP'),
      '#default_value' => $value->postal_code,
      '#required' => TRUE,
    );
/***********************************************************************/       
    $form['address']['field_latitude'] = array(
      '#type' => 'hidden',
      '#title' => t('Latitude:'),
       '#disabled' => TRUE,
       '#attributes' => array('id' => 'InsertRecordPICKUP_LATITUDE'),
      '#default_value' => $value->latitude,
    );
    $form['address']['field_longitude'] = array(
      '#type' => 'hidden',
      '#title' => t('Longitude:'),
      '#disabled' => TRUE,
      '#attributes' => array('id' => 'InsertRecordPICKUP_LONGITUDE'),
      '#default_value' => $value->longitude,
    );
    $form['markup_mandatory'] = array('#markup' => "<p class='text-danger'> * denotes mandatory fields </p>");
    $query = db_select('custom_users','c');
    $query->fields('c');
    $query->condition('id', $user_uid);
    $result = $query->execute();
    $data = $result->fetchObject();
    $role=$data->role;
  
   $form['back'] = array(
   		'#type' => 'markup',
   		'#markup' => '<span onClick="location.href=\'/profile/intro/\'" class="btn btn-default w-150 m-sm-r m-md-b back-button">Back</span>',
   ); 
    	$form['next'] = array(
    			'#type' => 'submit',
    			'#value' => t('Next'),
    			'#submit' => array('::newSubmissionHandlerNext'),
    			'#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r m-md-b")),
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

      if($result == 1){

          drupal_set_message('Your Location Details Completed Successfully.');

      } else {

         drupal_set_message($result);
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
      if($result=="1")
      {
      	//Update location contact status to 1 in custom users information table
      	$query = \Drupal::database()->update('custom_users_information');
      	$query->fields(['location_status' => 1]);
      	$query->condition('id', $user_location_uid);
      	$query->execute();
      }
      // set relative internal path
      $query = db_select('custom_users','c');
      $query->fields('c');
      $query->condition('id', $user_location_uid);
      $result = $query->execute();
      $data = $result->fetchObject();
      $role=$data->role;
     // if($role=="guest")
      //{
      //	$redirect_path_next = "/profile/thank-you";
      //	$url_next = url::fromUserInput($redirect_path_next);
     // }
     // else
     // {
      	$redirect_path_next = "/profile/contact";
      	$url_next = url::fromUserInput($redirect_path_next);
      	      	// set redirect
      	$form_state->setRedirectUrl($url_next);
     // }
      //drupal_set_message('Your Location Details Completed Successfully.');
  }
}
?>
