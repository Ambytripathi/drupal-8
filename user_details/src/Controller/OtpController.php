<?php
namespace Drupal\user_details\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use \Drupal\file\Entity\File;

/**
 * Provides route responses for the Example module.
 */
class OtpController extends ControllerBase {

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function thankuPage() {
    $element = array(
      '#markup' => 'Thanks you for verify your phone </br> <a href="/profile/contact">Ok</a>',
    );
    return $element;
  }
  
  public function thank_you_localexpert() {
    $element = array(
      '#markup' => '<div class="text-center p-xl-t p-xl-b m-lg-t m-lg-b"><h3 class="fw-thk">Thank you for completing your profile!</h3> <p>You will be able to create new experiences now.<br>Create your first experience today!</p><div class=""><a href="/profile/experience-creation/" class="btn btn-success">Create New Experience</a></div></div>',
    );
    return $element;
  }
  public function thank_you_diplomat() {
  	$element = array(
  			'#markup' => '<p><b>Thank you for submitting your profile!</b></p> <p><b>We are in the midst of reviewing your profile. You will be notified via email once your profile is approved. <br>You may then refer other Local Experts .</b></p></div>',
  	);
  	return $element;
  }
  public function thank_you_promoter() {
  	$element = array(
  			
  			'#markup' => '<p><b>Thank you for submitting your profile!</b></p> <p><b>You will be able to refer promoters once Ezplor Team verify your profile.</b></p></div>',
  	);
  	return $element;
  }
  public function thank_you() {
  	$element = array(
  			'#markup' => '<p><b>Thank you for completing your profile!</b></p>',
  	);
  	return $element;
  }
  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function getCity() {
	$country_name = $_POST['countryname'];
	//~ $country_name = 'India';
	$query = db_select('custom_country','cc');
    $query->fields('cc', ['id', 'country_code']);
    $query->condition('id', $country_name);
    $result = $query->execute();
    $data = $result->fetchAll();
    $cntry_id = array();
    foreach ($data as $value) {
		$cntry_id[$value->id] = $value->id;
    }
    //~ print '<pre>'; print_r($cntry_id); exit;
    
    $query = db_select('custom_state','cs');
    $query->fields('cs', ['id', 'state_name']);
    $query->condition('country_id', $cntry_id);
    $result = $query->execute();
    $data = $result->fetchAll();
    $state_details = array();
    foreach ($data as $value) {
		$state_details[$value->id] = $value->id;
    }
    
    $query = db_select('custom_city','cct');
    $query->fields('cct', ['id', 'city_name','description','state_id']);
    $query->condition('state_id', $state_details, 'IN');
    $result = $query->execute();
    $data = $result->fetchAll();
    $city_name = array();
    $i = 0;
    foreach ($data as $value) {
		$city_name[$i] = $value->city_name;
		$i++;
    }
    print_r(json_encode($city_name));
    //~ print_r(($city_name));
    exit;
    //~ return $city_name;
  }
  public function getcountrycode() {
  	$country_id = $_POST['country_id'];
  	$query = db_select('custom_country','cc');
  	$query->fields('cc', ['id', 'country_code']);
  	$query->condition('id', $country_id);
  	$result = $query->execute();
  	$data = $result->fetchObject();
  	$countrycode['country_code'] = strtolower($data->country_code);
  	echo json_encode($countrycode);
  	exit;
  	
  }
  public function ViewProfile($id) {
  	/*$user = \Drupal::currentUser()->getRoles();
  	 if(in_array("editors", $user))
  	 {
  	 $form['links'] = [
  	 '#theme' => 'item_list',
  	 '#items' => [
  	 Link::createFromRoute($this->t('&laquo; Back'), 'user_details.profilee'),
  	 ],
  	 ];
  	 }
  	 elseif(in_array("management", $user))
  	 {
  	 $form['links'] = [
  	 '#theme' => 'item_list',
  	 '#items' => [
  	 Link::createFromRoute($this->t('&laquo; Back'), 'user_details.profilem'),
  	 ],
  	 ];
  	 }*/
  	
  	$query_result = db_select('custom_users','c');
  	$query_result->fields('c');
  	$query_result->join('custom_users_information', 'cc', 'cc.user_id = c.user_id');
  	$query_result->fields('cc');
  	$query_result->condition('c.user_id',$id,'=');
  	$result = $query_result->execute();
  	$data = $result->fetchObject();
  	$given_name = $data->first_name;
  	$last_name = $data->last_name;
  	$nick_name= $data->nick_name;
  	$gender = $data->gender;
  	$dob = $data->dob;
  	$teaser = $data->intro;
  	$description = $data->description;
  	//$languages = $data->language;
  	$currency = $data->currency;
  	$license = $data->license_number;
  	$idno = $data->your_id;
  	$uploadid = $data->upload_id;
  	$company_name = $data->company_name;
  	$gst_no=$data->vat_number;
  	$uploadid = $data->upload_id;
  	$company_accnt = $data->company_account;
  	if($company_accnt=="1")
  	{
  		$company_accnt="Yes";
  	}
  	else
  	{
  		$company_accnt="No";
  	}
  	$country = $data->country;
  	$state = $data->state;
  	$city= $data->city;
  	$address1=$data->address;
  	$address2=$data->address2;
  	$postcode=$data->postal_code;
  	$mobileno=$data->country_code.$data->phone_number;
  	$emailid=$data->contact_email;
  	$payment_method=$data->payment_method;
  	$query1 = db_select('custom_currency','ccur');
  	$query1->fields('ccur');
  	$query1->condition('id', $currency);
  	$result1 = $query1->execute();
  	$data1 = $result1->fetchObject();
  	$currency=$data1->currency_code;
  	$query2 = db_select('custom_city','ct');
  	$query2->fields('ct');
  	$query2->condition('id', $city);
  	$result2 = $query2->execute();
  	$data2 = $result2->fetchObject();
  	$city=$data2->city_name;
  	$query4 = db_select('custom_country','cy');
  	$query4->fields('cy');
  	$query4->condition('id', $country);
  	$result4 = $query4->execute();
  	$data4 = $result4->fetchObject();
  	$country=$data4->country_name;
  	
  		$count_img = db_select('file_managed')
  		->condition('fid', $data->profile_image)
  		->countQuery()->execute()->fetchField();
  		if ($count_img!= 0) {
  			$profile_image_url = File::load($data->profile_image)->getFileUri();
  			$profile_imagename = File::load($data->profile_image)->getFileName();
  			$profile_image = file_create_url($profile_image_url);
  		}
  		else {
  			$profile_image = '/themes/explore/images/avatar3.png';
  		}
  	
  	
  		$count_id = db_select('file_managed')
  		->condition('fid', $data->upload_id)
  		->countQuery()->execute()->fetchField();
  		if ($count_id!= 0) {
  			$original_file = File::load($data->upload_id)->getFileUri();
  			$original_name = File::load($data->upload_id)->getFileName();
  			$url = file_create_url($original_file);
  		}
  	
  	if($payment_method=="bank_transfer")
  	{
  		$bank_name=$data->bank_name;
  		$bank_code=$data->bank_code;
  		$bank_address=$data->bank_address;
  		$bank_branch_code=$data->bank_branch_code;
  		$bank_branch=$data->bank_branch;
  		$bank_swift_code=$data->bank_swift_code;
  		$account_number=$data->account_number;
  		$account_first_name=$data->account_first_name;
  		$account_last_name=$data->account_last_name;
  		$account_holder_city=$data->account_holder_city;
  		$account_holder_address=$data->account_holder_address;
  		$account_holder_phone=$data->account_holder_country_code.$data->account_holder_phone;
  		$bank_swift_code=$data->bank_swift_code;
  		$account_number=$data->account_number;
  		$element = array(
  				
  				'#markup' => '<h3 class="fw-thk">Basic Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Given Name: </td><td>'.$given_name.'</td></tr><tr><td>Family Name: </td><td>'.$last_name.'</td></tr><tr><td>Nick Name: </td><td>'.$nick_name.'</td></tr><tr><td>Gender: </td><td>'.$gender.'</td></tr><tr><td>Date Of Birth: </td><td>'.$dob.'</td></tr><tr><td>Teaser: </td><td>'.$teaser.'</td></tr><tr><td>Description: </td><td>'.$description.'</td></tr></tr><tr><td>Currency: </td><td>'.$currency.'</td></tr><tr><td>Tour License: </td><td>'.$license.'</td></tr><tr><td>ID: </td><td>'.$idno.'</td></tr><tr><td>Company Account: </td><td>'.$company_accnt.'</td></tr><tr><td>Company Name: </td><td>'.$company_name.'</td></tr><tr><td>GST/VAT No: </td><td>'.$gst_no.'</td></tr><tr><td>Uploaded ID: </td><td><div class="upload_preview"><span class="file-icon"><span class="icon glyphicon glyphicon-picture text-primary" aria-hidden="true"></span></span><span class="file-link"><a href="'.$url.'" target="_blank">'.$original_name.'</a></span></div></td></tr><tr><td>Profile Image: </td><td><div class="upload_preview"><span class="file-icon"><span class="icon glyphicon glyphicon-picture text-primary" aria-hidden="true"></span></span><span class="file-link"><a href="'.$profile_image.'" target="_blank">'.$profile_imagename.'</a></span></div></td></tr></tbody></table></div></div></div>
<h3 class="fw-thk">Location Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Country </td><td>'.$country.'</td></tr><tr><td>City: </td><td>'.$city.'</td></tr><tr><td>Address1: </td><td>'.$address1.'</td></tr><tr><td>Address2: </td><td>'.$address2.'</td></tr><tr><td>State: </td><td>'.$state.'</td></tr><tr><td>Postcode: </td><td>'.$postcode.'</td></tr></tbody></table></div></div></div>
 <h3 class="fw-thk">Contact Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Mobile Number </td><td>'.$mobileno.'</td></tr><tr><td>Email ID: </td><td>'.$emailid.'</td></tr></tbody></table></div></div></div>
 <h3 class="fw-thk">Payment Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Payment Method: </td><td>'.$payment_method.'</td></tr><tr><td>Bank Name: </td><td>'.$bank_name.'</td></tr><tr><td>Bank Code: </td><td>'.$bank_code.'</td></tr><tr><td>Bank Address: </td><td>'.$bank_address.'</td></tr><tr><td>Bank branch code: </td><td>'.$bank_branch_code.'</td></tr><tr><td>Bank branch: </td><td>'.$bank_branch.'</td></tr><tr><td>Bank Swift code: </td><td>'.$bank_swift_code.'</td></tr><tr><td>Account number: </td><td>'.$account_number.'</td></tr><tr><td>Account holder first name: </td><td>'.$account_first_name.'</td></tr><tr><td>Account holder last name : </td><td>'.$account_last_name.'</td></tr><tr><td>Account holder city: </td><td>'.$account_holder_city.'</td></tr><tr><td>Account holder address: </td><td>'.$account_holder_address.'</td></tr><tr><td>Account holder phone number: </td><td>'.$account_holder_phone.'</td></tr></tbody></table></div></div></div>',
  		);
  	}
  	elseif($payment_method=="paypal")
  	{
  		$paypal_email=$data->paypal_email;
  		$element = array(
  				
  				'#markup' => '<h3 class="fw-thk">Basic Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Given Name: </td><td>'.$given_name.'</td></tr><tr><td>Family Name: </td><td>'.$last_name.'</td></tr><tr><td>Nick Name: </td><td>'.$nick_name.'</td></tr><tr><td>Gender: </td><td>'.$gender.'</td></tr><tr><td>Date Of Birth: </td><td>'.$dob.'</td></tr><tr><td>Teaser: </td><td>'.$teaser.'</td></tr><tr><td>Description: </td><td>'.$description.'</td></tr><tr><td>Currency: </td><td>'.$currency.'</td></tr><tr><td>Tour License: </td><td>'.$license.'</td></tr><tr><td>ID: </td><td>'.$idno.'</td></tr><tr><td>Company Account: </td><td>'.$company_accnt.'</td></tr><tr><td>Company Name: </td><td>'.$company_name.'</td></tr><tr><td>GST/VAT No: </td><td>'.$gst_no.'</td></tr><tr><td>Uploaded ID: </td><td><div class="upload_preview"><span class="file-icon"><span class="icon glyphicon glyphicon-picture text-primary" aria-hidden="true"></span></span><span class="file-link"><a href="'.$url.'" target="_blank">'.$original_name.'</a></span></div></td></tr><tr><td>Profile Image: </td><td><div class="upload_preview"><span class="file-icon"><span class="icon glyphicon glyphicon-picture text-primary" aria-hidden="true"></span></span><span class="file-link"><a href="'.$profile_image.'" target="_blank">'.$profile_imagename.'</a></span></div></td></tr></tbody></table></div></div></div>
<h3 class="fw-thk">Location Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Country </td><td>'.$country.'</td></tr><tr><td>City: </td><td>'.$city.'</td></tr><tr><td>Address1: </td><td>'.$address1.'</td></tr><tr><td>Address2: </td><td>'.$address2.'</td></tr><tr><td>State: </td><td>'.$state.'</td></tr><tr><td>Postcode: </td><td>'.$postcode.'</td></tr></tbody></table></div></div></div>
 <h3 class="fw-thk">Contact Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Mobile Number </td><td>'.$mobileno.'</td></tr><tr><td>Email ID: </td><td>'.$emailid.'</td></tr></tbody></table></div></div></div>
 <h3 class="fw-thk">Payment Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Payment Method: </td><td>'.$payment_method.'</td></tr><tr><td>Paypal Email ID: </td><td>'.$paypal_email.'</td></tr></tbody></table></div></div></div>',
  		);
  	}
  	else
  	{
  		
  		$element = array(
  				
  				'#markup' => '<h3 class="fw-thk">Basic Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Given Name: </td><td>'.$given_name.'</td></tr><tr><td>Family Name: </td><td>'.$last_name.'</td></tr><tr><td>Nick Name: </td><td>'.$nick_name.'</td></tr><tr><td>Gender: </td><td>'.$gender.'</td></tr><tr><td>Date Of Birth: </td><td>'.$dob.'</td></tr><tr><td>Teaser: </td><td>'.$teaser.'</td></tr><tr><td>Description: </td><td>'.$description.'</td></tr><tr><td>Currency: </td><td>'.$currency.'</td></tr><tr><td>Tour License: </td><td>'.$license.'</td></tr><tr><td>ID: </td><td>'.$idno.'</td></tr><tr><td>Company Account: </td><td>'.$company_accnt.'</td></tr><tr><td>Company Name: </td><td>'.$company_name.'</td></tr><tr><td>GST/VAT No: </td><td>'.$gst_no.'</td></tr><tr><td>Uploaded ID: </td><td><div class="upload_preview"><span class="file-icon"><span class="icon glyphicon glyphicon-picture text-primary" aria-hidden="true"></span></span><span class="file-link"><a href="'.$url.'" target="_blank">'.$original_name.'</a></span></div></td></tr><tr><td>Profile Image: </td><td><div class="upload_preview"><span class="file-icon"><span class="icon glyphicon glyphicon-picture text-primary" aria-hidden="true"></span></span><span class="file-link"><a href="'.$profile_image.'" target="_blank">'.$profile_imagename.'</a></span></div></td></tr></tbody></table></div></div></div>
<h3 class="fw-thk">Location Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Country </td><td>'.$country.'</td></tr><tr><td>City: </td><td>'.$city.'</td></tr><tr><td>Address1: </td><td>'.$address1.'</td></tr><tr><td>Address2: </td><td>'.$address2.'</td></tr><tr><td>State: </td><td>'.$state.'</td></tr><tr><td>Postcode: </td><td>'.$postcode.'</td></tr></tbody></table></div></div></div>
 <h3 class="fw-thk">Contact Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Mobile Number </td><td>'.$mobileno.'</td></tr><tr><td>Email ID: </td><td>'.$emailid.'</td></tr></tbody></table></div></div></div>
 <h3 class="fw-thk">Payment Details</h3><div class="row"><div class="col-md-6"><div class="table-responsive"><table class="table table-striped"><tbody><tr><td>Payment Method: </td><td>'.$payment_method.'</td></tr></tbody></table></div></div></div>',
  		);
  	}
  	return $element;
  }
}

?>
