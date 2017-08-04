<?php
/**
 * @file
 * Contains \Drupal\amazing_forms\Form\IntroduceForm.
 */

namespace Drupal\user_details\Form;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\user\Entity\User;
use Drupal\image\Entity\ImageStyle;
use \Drupal\file\Entity\File;
use Drupal\std_hacks\Controller\APIController;
use Drupal\Core\Url;
use Drupal\Core\Database;



/**
 * Introduce form.
 */
class IntroduceForm extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
	   
    return 'introduce_forms_introduce_form';
  
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	$user_uid = \Drupal::currentUser()->id();
	
	$query = db_select('custom_users_information','cui');
    $query->fields('cui', ['id', 'first_name', 'last_name', 'nick_name', 'profile_image', 'gender', 'dob', 'description', 'designation', 'license_number', 'language', 'currency', 'intro', 'your_id', 'upload_id','company_account','company_name','vat_number']);
    $query->condition('id', $user_uid);
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
    }
    /************************** Currency *************************/
    $currency_query = db_select('custom_currency','cc');
    $currency_query->fields('cc', ['id', 'currency_code','currency_name']);
    $currency_query->orderBy('currency_name', 'ASC');//ORDER BY created
    $currency_result = $currency_query->execute();
    $currency_data = $currency_result->fetchAll();
    $currency_code = array();
    foreach ($currency_data as $currency_value) {
  		$currency_code[$currency_value->id] = $currency_value->currency_code;
    }
    /************************** Language *************************/
    $language_query = db_select('custom_language','cl');
    $language_query->fields('cl', ['id','language_name']);
    $language_query->orderBy('id', 'ASC');
    $language_result = $language_query->execute();
    $language_data = $language_result->fetchAll();
    $language_code = array();
    foreach ($language_data as $language_value) {
  		$language_code[$language_value->id] = $language_value->language_name;
    }
    
    //~ $newDate = date("d/m/Y", strtotime($value->dob));
    $default_language = explode(",",$value->language);
    $form['#attached']['library'][] = 'user_details/user-details';
	if(isset($value->profile_image)){
		$count = db_select('file_managed')
			->condition('fid', $value->profile_image)
			->countQuery()->execute()->fetchField();
		if ($count != 0) { 
			$original_image = File::load($value->profile_image)->getFileUri();
			$style = ImageStyle::load('thumbnail');
			$uri = $style->buildUri($original_image);
			$url = $style->buildUrl($original_image);
		}
		else {
			$url = '/themes/explore/images/avatar3.png';
		  }
	} else {
    $url = '/themes/explore/images/avatar3.png';
  }

    $form['thumbnail_preview'] = array(
      '#type' => 'item',
      '#markup' => '<img src="'.$url.'" class="img-circle" width="100px" height="100px">',
      '#prefix' => '<div class="profile-image-block form-group"><div class="img-block">',
      '#suffix' => '<span class="display-icon"><i class="fa fa-camera" aria-hidden="true"></i> Choose your profile pic</span></div>',
    );
	
    $form['user-picture'] = array(
      '#type' => 'managed_file',
      '#title' => t(''),
      '#upload_location' => 'public://images/',
      '#default_value' =>$value->profile_image,
      '#upload_validators' => array('file_validate_extensions' => array('jpg png jpeg'),
									'file_validate_image_resolution' => array('600x600'),
      								'file_validate_size' => array(3*1024*1024),
      ),
    );  

	$form['field_firstname'] = array(
      '#type' => 'textfield',
      '#title' => t('Given name'),
      '#prefix' => '</div><div id="name-block" class="row"><div class="col-md-6">',
      '#suffix' => '</div>',      
      '#default_value' =>$value->first_name,
      '#required' => TRUE,
    );
    $form['field_lastname'] = array(
      '#type' => 'textfield',
      '#title' => t('Family name/surname'),
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>',  
      '#default_value' =>$value->last_name,
      '#required' => TRUE,
    ); 
    $form['field_nickname'] = array(
      '#type' => 'textfield',
      '#title' => t('Nickname'),
      '#default_value' =>$value->nick_name,
      '#required' => TRUE,
    		'#attributes' => array(
    				'title' => t("This is the name you’d like to be called."),
    				'data-toggle'=>'tooltip'
    		)
    ); 
    $form['field_gender'] = array(
	  '#type' => 'radios',
	  '#title' => t('Gender'),
	  '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
      '#suffix' => '</div>',
	  '#options' => array(
				  'male' => t('Male'),
				  'female' => t('Female'), 
				),
	  '#default_value' => $value->gender,
	  '#required' => TRUE,
	);
/*******************************************************************/    
     $form['birthday'] = array(
	  '#type' => 'fieldset',
	  '#title' => 'Date Of Birth',
	  '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>',
	);
	$newDate = (explode("-",$value->dob));
	$day = array();	
	for($i = 1; $i <= 31; $i++){
		$days = sprintf("%02d",$i);
		$day[$days] = $days;
	} 
	
    $form['birthday']['field_day'] = array(
      '#type' => 'select',
      '#prefix' => '<div class="row"><div class="col-md-4">',
      '#suffix' => '</div>',
      '#empty_option' => $this->t('Day'),
      '#options' => $day,
      '#default_value' => $newDate[2],
      '#required' => TRUE,
    ); 
    $month = array();	
	for($i = 1; $i <= 12; $i++){
		$months = sprintf("%02d",$i);
		$month[$months] = $months;
	}    
    $form['birthday']['field_month'] = array(
      '#type' => 'select',
      '#prefix' => '<div class="col-md-4">',
      '#suffix' => '</div>',
      '#empty_option' => $this->t('Month'),
      '#options' => $month,
      '#default_value' => $newDate[1],
      '#required' => TRUE,
    ); 
    $current_year = date("Y");
    $givenyear = $current_year - 100;
    $year = array();	
	for($i = $current_year; $i >= $givenyear; $i--){
		$year[$i] = $i;
	}
    $form['birthday']['field_year'] = array(
      '#type' => 'select',
      '#prefix' => '<div class="col-md-4">',
      '#suffix' => '</div></div>',
      '#empty_option' => $this->t('Year'),
      '#options' => $year,
      '#default_value' => $newDate[0],
      '#required' => TRUE,
    ); 

    $form['field_teaser'] = array(
      '#type' => 'textfield',
      '#title' => t('Teaser'),
       '#default_value' =>$value->intro,
       '#required' => TRUE,
    		'#attributes' => array(
    				'title' => t("Describe to us in 10 words or less about yourself <br/> (eg: I’m a Rockstar tour guide with a passion for tennis)"),
    				'data-toggle'=>'tooltip'
    		)
    ); 
 		
	$user = \Drupal::currentUser()->getRoles();
	if(in_array("local_experts", $user)) {  
		$form['field_description'] = array(
				'#type' => 'textarea',
				'#title' => t('Description'),
				'#default_value' => $value->description,
				'#required' => TRUE,
				);
		$form['field_language'] = array(
		  '#type' => 'checkboxes',
		  '#title' => t('Language you speak'),
		  '#rows' => 10,
		  '#multiple' => true,		  
		  '#empty_option' => $this->t('-Please Select Language-'),
		  '#options' => $language_code,
		  '#default_value' => $default_language,
		  '#required' => TRUE,
		); 
	}
	else 
	{
		$form['field_description'] = array(
				'#type' => 'textarea',
				'#title' => t('Description'),
				'#default_value' => $value->description,
				'#required' => TRUE,
		);  
	}
    $form['field_currency'] = array(
      '#type' => 'select',
      '#title' => t('Currency'),
      '#empty_option' => $this->t('-Please Select Currency-'),
      '#options' => $currency_code,
      '#default_value' => $value->currency,
      '#required' => TRUE,
    ); 
    $form['field_tour_license'] = array(
      '#type' => 'textfield',
      '#title' => t('Tour License'),
      '#default_value' =>$value->license_number,
       ); 
   $form['field_your_id'] = array(
      '#type' => 'textfield',
      '#title' => t('Your ID (IC / Passport) Number'),
      '#default_value' =>$value->your_id,
   		'#required' => TRUE,
    ); 
    
    $form['upload_details'] = array(
	  '#type' => 'fieldset',
	);
    
    $form['upload_details']['field_upload_id'] = array(
      '#type'          => 'managed_file',
      '#title'         => t('Upload Your Identification Document here'),
      '#description' => t('Allowed extensions: jpg pdf png jpeg docx doc'),
      '#upload_location' => 'public://uploaded_id/',
      '#default_value' => $value->upload_id,
      '#upload_validators' => array(
  		'file_validate_extensions' => array('jpg pdf png jpeg docx doc'),
  		// Pass the maximum file size in bytes
  		'file_validate_size' => array(3*1024*1024),)
    );    
    if(isset($value->upload_id)){
    	$count = db_select('file_managed')
    	->condition('fid', $value->upload_id)
    	->countQuery()->execute()->fetchField();
    	if ($count != 0) {
    		$original_file = File::load($value->upload_id)->getFileUri();
    		$original_name = File::load($value->upload_id)->getFileName();
    		$url = file_create_url($original_file);
    		
    		$form['upload_details']['thumbnail_uploadid_preview'] = array(
    				'#type' => 'item',
    				'#markup' => '<div class="upload_preview"><span class="file-icon"><span class="icon glyphicon glyphicon-picture text-primary" aria-hidden="true"></span></span><span class="file-link"><a href="'.$url.'" target="_blank">'.$original_name.'</a></span></div>'
    		);
    	}
    }
    $form['field_company_account'] = array(
    		'#type' => 'checkboxes',
    		'#title' => t('Company Account'),
    		'#options' => array('1' => t('Check this box if you are using Ezplor under a company name.')),
    		'#default_value' => array($value->company_account),
    );
    
    $form['company_details'] = array(
    		'#type' => 'fieldset',
    		'#title' => t('Company Account'),
    		'#prefix' => '<div class="checkbox m-lg-t">',
    		'#suffix' => '</div>',
    		'#states' => array(
    				'visible' => array(
    						':input[name="field_company_account[1]"]' => array('checked' => TRUE),
    				),
    		),
    		'#return_value' => 1,
    );
       $form['company_details']['field_company_name'] = array(
	    		'#type' => 'textfield',
	    		'#title' => t('Company Name'),
	    		'#default_value' =>$value->company_name,
	    );
	    $form['company_details']['field_vat_number'] = array(
	    		'#type' => 'textfield',
	    		'#title' => t('VAT/GST Number'),
	    		'#default_value' =>$value->vat_number,
	    ); 
    $form['markup_mandatory'] = array('#markup' => "<p class='text-danger'> * denotes mandatory fields </p>");
   
    $form['next'] = array(
	  '#type' => 'submit',
	  '#value' => t('Next'),
    '#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r")),
	  '#submit' => array('::newSubmissionHandlerNext'),
	);	
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
	// $company_check = $form_state->getValue('field_company_account');
	 $nickname = $form_state->getValue('field_nickname');
	 if(preg_match('/[^A-Za-z]/', $nickname)) {
	 	$form_state->setErrorByName('field_nickname', t('Only alphabets allowed for nickname.'));
	 }
	 $user_uid = \Drupal::currentUser()->id();
	 $nickname_count =db_select('custom_users_information')
	 ->condition('nick_name', $nickname)
	 ->condition('user_id', $user_uid, '<>')
	 ->countQuery()	 
	 ->execute()->fetchField();
	 if($nickname_count>0)
	 {
	 	$form_state->setErrorByName('field_nickname', t('This Nickname already taken.Please enter another one.'));
	 }
	 //if($company_check[1] == 1){
	
	 $query = db_select('custom_users_information','cui');
	 $query->fields('cui', ['id',  'upload_id']);
	 $query->condition('id', $user_uid);
	 $results = $query->execute();
	 $datas = $results->fetchObject();
	 $uploaded_id = $datas->upload_id;
		 $upload_id = $form_state->getValue('field_upload_id');
		 if(empty($uploaded_id)){
			 if (empty($upload_id)){
				$form_state->setErrorByName('field_upload_id', t('Please upload Your ID.'));
			}
		 }
	//}
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	  
	  $user_introduce_uid = \Drupal::currentUser()->id();
	  $profile_image = $form_state->getValue('user-picture');
	  $first_name = $form_state->getValue('field_firstname');
	  $last_name = $form_state->getValue('field_lastname');
	  $nick_name = $form_state->getValue('field_nickname');
	  $gender = $form_state->getValue('field_gender');
      $teaser = $form_state->getValue('field_teaser');
	  //~ $dob = $form_state->getValue('field_dob');
	  //~ $newDate = date("Y-m-d", strtotime($dob));
	  $dob_day = $form_state->getValue('field_day');
	  $dob_month = $form_state->getValue('field_month');
	  $dob_year = $form_state->getValue('field_year');
	  $newDate = $dob_year.'-'.$dob_month.'-'.$dob_day; 
	  $description = $form_state->getValue('field_description');
	  //$designation = $form_state->getValue('field_designation');
	  $designation="";
	  $license_number = $form_state->getValue('field_tour_license');
	  $lang = array_filter($form_state->getValue('field_language'));
	  $language = implode(",",$lang);
	  $currency = $form_state->getValue('field_currency');
	  $your_id = $form_state->getValue('field_your_id');
	  $upload_id = $form_state->getValue('field_upload_id');
	  $company_name = $form_state->getValue('field_company_name');
	  $company_account = $form_state->getValue('field_company_account');
	  //$company_account = 0;
	  $cmpaccnt= $company_account[1];
	  $vat_number = $form_state->getValue('field_vat_number');
	  $query = db_select('custom_users_information','cui');
      $query->fields('cui', ['id', 'profile_image', 'upload_id']);
      $query->condition('id', $user_introduce_uid);
      $results = $query->execute();
      $datas = $results->fetchAll();
      foreach ($datas as $values) {
      }

      
      if(empty($profile_image)){
	       $profile_image = $values->profile_image;
	  }
	  else{
		  $profile_image = $profile_image[0];
	  }
	  
	  if(empty($upload_id)){
	       $upload_id = $values->upload_id;
	  }
	  else{
		  $upload_id = $upload_id[0];
	  }
	  
	  //print $profile_image; exit;
	  
	  //~ print $profile_image; exit;
	  if(isset($profile_image)){
		  $file = File::load ($profile_image);
		  /* Set the status flag permanent of the image file object */
		  $file->setPermanent();
		  /* Save the file in database ( "managed_file" table) */
		  $file->save();
	  }
	  if(isset($upload_id)){
		  $file_up_id = File::load ($upload_id);
		  /* Set the status flag permanent of the image file object */
		  $file_up_id->setPermanent();
		  /* Save the file in database ( "managed_file" table) */
		  $file_up_id->save();
		  file_usage_add($file_up_id, 'user_details','id', $user_introduce_uid);
	  }
	  
	  $inputarray = array("uid"=>$user_introduce_uid,"profile_image"=>$profile_image,"first_name"=>$first_name,"last_name"=>$last_name,"gender"=>$gender,"teaser"=>$teaser,"dob"=>$newDate,"description"=>$description,"designation"=>$designation,"license_number"=>$license_number,"language"=>$language,"currency"=>$currency,"your_id"=>$your_id,"upload_id"=>$upload_id,"nick_name"=>$nick_name,"company_account"=>$cmpaccnt,"company_name"=>$company_name,"vat_number"=>$vat_number);

      $classobj = new APIController();

      $result = $classobj->intro_details_update($inputarray);

      if($result == 1){

          drupal_set_message('Your Introduce Yourself Completed Successfully.');

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
      $user_uid = \Drupal::currentUser()->id();
	  $profile_image = $form_state->getValue('user-picture');
	  $first_name = $form_state->getValue('field_firstname');
	  $last_name = $form_state->getValue('field_lastname');
	  $nick_name = $form_state->getValue('field_nickname');
	  $gender = $form_state->getValue('field_gender');
      $teaser = $form_state->getValue('field_teaser');
	  //~ $dob = $form_state->getValue('field_dob');
	  //~ $newDate = date("Y-m-d", strtotime($dob));
	  $dob_day = $form_state->getValue('field_day');
	  $dob_month = $form_state->getValue('field_month');
	  $dob_year = $form_state->getValue('field_year');
	  $newDate = $dob_year.'-'.$dob_month.'-'.$dob_day; 
	  $description = $form_state->getValue('field_description');
	 // $designation = $form_state->getValue('field_designation');
	  $designation="";
	  $license_number = $form_state->getValue('field_tour_license');
	  $lang = array_filter($form_state->getValue('field_language'));
	  $language = implode(",",$lang);
	  $currency = $form_state->getValue('field_currency');
	  $your_id = $form_state->getValue('field_your_id');
	  $upload_id = $form_state->getValue('field_upload_id');
	  $company_name = $form_state->getValue('field_company_name');
	  $company_account = $form_state->getValue('field_company_account');
	  $cmpaccnt= $company_account[1];
	  $vat_number = $form_state->getValue('field_vat_number');
	  
	  $query = db_select('custom_users_information','cui');
      $query->fields('cui', ['id', 'profile_image', 'upload_id']);
      $query->condition('id', $user_uid);
      $results = $query->execute();
      $datas = $results->fetchAll();
      foreach ($datas as $values) {
      }
      if(empty($profile_image)){
	       $profile_image = $values->profile_image;
	  }
	  else{
		  $profile_image = $profile_image[0];
	  }
	  //~ print '<pre>'; print_r($values->upload_id); exit;
	  if(empty($upload_id)){
	       $upload_id = $values->upload_id;
	  }
	  else{
		  $upload_id = $upload_id[0];
	  }
	  
	  //~ print $profile_image; exit;
	  if(isset($profile_image)){
		  $file = File::load ($profile_image);
		  /* Set the status flag permanent of the image file object */
		  $file->setPermanent();
		  /* Save the file in database ( "managed_file" table) */
		  $file->save();
	  }
	  if(isset($upload_id)){
		  $file_up_id = File::load ($upload_id);
		  /* Set the status flag permanent of the image file object */
		  $file_up_id->setPermanent();
		  /* Save the file in database ( "managed_file" table) */
		  $file_up_id->save();
	  }
	 // try {
	  $inputarray = array("uid"=>$user_uid,"profile_image"=>$profile_image,"first_name"=>$first_name,"last_name"=>$last_name,"gender"=>$gender,"teaser"=>$teaser,"dob"=>$newDate,"description"=>$description,"designation"=>$designation,"license_number"=>$license_number,"language"=>$language,"currency"=>$currency,"your_id"=>$your_id,"upload_id"=>$upload_id,"nick_name"=>$nick_name,"company_account"=>$cmpaccnt,"company_name"=>$company_name,"vat_number"=>$vat_number);
      $classobj = new APIController();
      
      $result = $classobj->intro_details_update($inputarray);
	//  }
	//  catch (\Exception $ex)
	 // {
	 // 	drupal_set_message($ex->getMessage());
	  //	$result = $ex->getMessage();
	  //}
     
      if($result=="1")
      {
      	//Update profile introduce status to 1 in custom users information table
      	$query = \Drupal::database()->update('custom_users_information');
      	$query->fields(['introduce_status' => 1]);
      	$query->condition('id', $user_uid);
      	$query->execute();
      	// set relative internal path
      	$redirect_path_next = "/profile/location/";
      	$url_next = url::fromUserInput($redirect_path_next);	
      	// set redirect
      	$form_state->setRedirectUrl($url_next);
      }
      else 
      {
      	drupal_set_message($result,'error');
      }
	  
  }
}
	
?>
