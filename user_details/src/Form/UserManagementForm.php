<?php

namespace Drupal\user_details\Form;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ChangedCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class UserManagementForm extends FormBase {

  public function getFormId() {

    return 'user_management_form';
  
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
  	$user_role = \Drupal::currentUser()->getRoles();
    $info_values = '';

    if($user_role[1]=="management" || $user_role[1]=="administrator")
    {
    	$info_values .= '<div class="table-responsive"><table class="table table-hover table-striped"><thead>
    <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Roles</th><th>Status</th><th>Country</th><th>Type Of Signup</th><th>Actions</th><th>Edit</th><th>View Profile</th></tr></thead><tbody>';
    }
    else
    {
    	$info_values .= '<div class="table-responsive"><table class="table table-hover table-striped"><thead>
    <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Roles</th><th>Status</th><th>Country</th><th>Type Of Signup</th><th>View Profile</th></tr></thead><tbody>';
    	
    }
    
    $query_resultd = db_select('custom_users','cuser');
    $query_resultd->fields('cuser', ["id","email","status","role","user_id","parent_id"]);
    $query_resultd->condition('role', "diplomats");
    $query_resultd->join('custom_users_information', 'ccuser', 'ccuser.user_id = cuser.user_id');
    $query_resultd->fields('ccuser', ['first_name','last_name','country']);
    $query_resultd->orderBy("first_name","ASC");
    $resultd = $query_resultd->execute();
    $datad = $resultd->fetchAll();
    $dipname = array();
    foreach ($datad as $values) {
    	$dipname[$values->id] = $values->first_name.' '.$values->last_name;
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
    $query_result = db_select('custom_users','c');
    $query_result->fields('c', ["id","email","status","role","user_id","parent_id"]);
    $query_result->join('custom_users_information', 'cc', 'cc.user_id = c.user_id');
    $query_result->fields('cc', ['first_name','last_name','country']);
    $query_result->orderBy("id","DESC");
    $result = $query_result->execute();
    $data = $result->fetchAll();

    foreach($data as $row){

      $roles = $row->role;
      $parentid=$row->parent_id;
      $countryid=$row->country;
      $query_c = db_select('custom_country','cou');
      $query_c->fields('cou', ["id","country_name"]);
      $query_c->condition("id",$countryid);
      $result_c = $query_c->execute();
      $data_c = $result_c->fetchObject();
      $country=$data_c->country_name;
      
      $query1 = db_select('custom_users_information','cust');
      $query1->fields('cust', ['first_name', 'last_name']);
      $query1->condition('user_id', $parentid);
      $result1 = $query1->execute();
      $data1 = $result1->fetchObject();
      if($data1!="")
      {
      	$refername=$data1->first_name.' '.$data1->last_name;
      }
      else
      {
      	$refername="Nida Direct";
      }
      if($row->status == 0){

        $status = 'In-Active';
        $action = '<div id="'.$row->id.'" class="activate-operation btn btn-success Activate">Activate</div>';
      //  $rejection = '<div id="'.$row->id.'" class="reject-operation btn btn-danger reject">Reject</div>';

      } else if($row->status == 1){

        $status = 'Active';
        $action = '<div id="'.$row->id.'" class="activate-operation btn btn-danger De-Activate">De-Activate</div>';
       // $rejection = '<div id="'.$row->id.'" class="reject-operation btn btn-danger reject">Reject</div>';

      } else if($row->status == 2){

        $status = 'Rejected';
        $action = '<div id="'.$row->id.'" class="no-action btn btn-primary">No Action</div>';
        //$rejection = '<div id="'.$row->id.'" class="no-action btn btn-primary">No Action</div>';

      }
    
      if($user_role[1]=="management" || $user_role[1]=="administrator")
      {
      	$info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$status.'</td><td>'.$country.'</td><td>'.$refername.'</td><td>'.$action.'</td><td><a class="btn btn-success" href="/user/'.$row->id.'/edit">Edit</a></td><td><a class="btn btn-primary" href="/profile/'.$row->id.'/view">View Profile</a></td></tr>';
      }
      else
      {
      	$info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$status.'</td><td>'.$country.'</td><td>'.$refername.'</td><td><a class="btn btn-primary" href="/profile/'.$row->id.'/view">View Profile</a></td></tr>';
      }
      //$info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$status.'</td><td>'.$action.'</td><td>'.$rejection.'</td><td><a class="btn btn-success" href="/user/'.$row->id.'/edit">Edit</a></td></tr>';

    }

    $info_values .= '</tbody></table></div>';

    $form['user_id'] = array(
      '#type' => 'textfield',
      '#prefix' => '<div class="row"><div class="col-md-6">',
      '#title' => t('User ID'),
      '#default_value' => '',
      '#suffix' => '</div>'
    );
    
    $form['email'] = array(
      '#type' => 'textfield',
      '#title' => t('Email'),
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>',
      '#default_value' => '',
    );

    $form['status'] = array(
      '#type' => 'select',
      '#prefix' => '<div class="row"><div class="col-md-6">',
      '#suffix' => '</div>',
      '#title' => t('status'),
      '#empty_option' => t("All"),
      '#options' => array("0"=>"In-Active","1"=>"Active"),
    );
    $form['role'] = array(
    		'#type' => 'select',
    		'#title' => t('Role'),
    		'#empty_option' => t("All"),
    		'#options' => array("local_experts"=>"Local Experts","diplomats"=>"Diplomats","management"=>"Management","editors"=>"Editors","guest"=>"Guest"),
    		'#prefix' => '<div class="col-md-6">',
    		'#suffix' => '</div></div>',
    		
    );
    $form['country'] = array(
    		'#type' => 'select',
    		'#title' => t('Country'),
    		'#empty_option' => t("All"),
    		'#options' => $cntry_name,
    		'#prefix' => '<div class="row"><div class="col-md-6">',
    		'#suffix' => '</div>'
    );
    
    $form['type'] = array(
    		'#type' => 'select',
    		'#prefix' => '<div class="col-md-6">',
    		'#suffix' => '</div></div>',
    		'#title' => t('Type Of Signup'),
    		'#empty_option' => t("All"),
    		'#options' => array('Nida Direct','Diplomats'=>$dipname),
    );
    
    
    $form['submit'] = array(
      '#type' => 'button',
      '#value' => $this->t('Search'),
      '#button_type' => 'primary',
      '#ajax' => array(
          'callback' => '::_offersfiltersubmit',
          'effect' => 'fade',
          'progress' => array(
          'type' => 'throbber',
          'message' => "Searching",
          ),
      ),
    );

    $form['reset'] = array(
      '#type' => 'button',
      '#value' => $this->t('Reset'),
      '#button_type' => 'success',
      '#suffix' => '<div id="offers-informations">'.$info_values.'</div>'
    );

   
    return $form;
  
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    drupal_set_message("sdf");

  }

  public function _offersfiltersubmit(array &$form, FormStateInterface $form_state) {
    
     $ajax_response = new AjaxResponse();
     $user_id = $form_state->getValue("user_id");
     $email = $form_state->getValue("email");
     $status = $form_state->getValue("status");
     $role = $form_state->getValue("role");
     $country = $form_state->getValue("country");
     $user_role = \Drupal::currentUser()->getRoles();
     $type = $form_state->getValue("type");
    $info_values = '';
    if($user_role[1]=="management" || $user_role[1]=="administrator")
    {
   		$info_values .= '<div class="table-responsive"><table class="table table-hover table-striped"><thead>
    <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Roles</th><th>Status</th><th>Country</th><th>Type Of Signup</th><th>Actions</th><th>Edit</th><th>View Profile</th></tr></thead><tbody>';
    }
    else
    {
    	$info_values .= '<div class="table-responsive"><table class="table table-hover table-striped"><thead>
    <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Roles</th><th>Status</th><th>Country</th><th>Type Of Signup</th><th>View Profile</th></tr></thead><tbody>';
    	
    }
    $query_result = db_select('custom_users','c');
    $query_result->fields('c', ["id","email","status","role","user_id","parent_id"]);
    $query_result->join('custom_users_information', 'cc', 'cc.user_id = c.user_id');
    $query_result->fields('cc', ['first_name','last_name','country']);
    if(isset($status) && $status!=""){ $query_result->condition("c.status",$status); }
    if(isset($role) && $role!="") {  $query_result->condition('c.role', "%" . $query_result->escapeLike($role) . "%", 'LIKE'); }
    //if(isset($role) && $role!=""){ $query_result->condition("c.role",$role); }
    if(isset($country) && $country!=""){ $query_result->condition("cc.country",$country); }
    if(isset($user_id) && $user_id > 2){ $query_result->condition("c.id",$user_id); }
    if(isset($email) && strlen($email) > 2){ $query_result->condition("c.email",$email); }
    if(isset($type))
    {
    	if($type == "0")
    	{
    		$query_result->isNull('c.parent_id');
    		
    	}
    	else if ($type == "")
    	{
    		
    	}
    	else if ($type != "0")
    	{
    		
    		$query_result->condition('c.parent_id', $type);
    	}
    }
    
    
    $query_result->orderBy("c.id","DESC");
    
    $result = $query_result->execute();
    $data = $result->fetchAll();

    
    
    if(count($data) > 0){

      foreach($data as $row){

        $roles = $row->role;
        $countryid=$row->country;
        $parentid=$row->parent_id;
        $query_c = db_select('custom_country','cou');
        $query_c->fields('cou', ["id","country_name"]);
        $query_c->condition("id",$countryid);
        $result_c = $query_c->execute();
        $data_c = $result_c->fetchObject();
        $country=$data_c->country_name;
        $query1 = db_select('custom_users_information','cust');
        $query1->fields('cust', ['first_name', 'last_name']);
        $query1->condition('user_id', $parentid);
        $result1 = $query1->execute();
        $data1 = $result1->fetchObject();
        if($data1!="")
        {
        	$refername=$data1->first_name.' '.$data1->last_name;
        }
        else
        {
        	$refername="Nida Direct";
        }
        if($row->status == 0){

          $status = 'In-Active';
          $action = '<div id="'.$row->id.'" class="activate-operation btn btn-success Activate">Activate</div>';
        //  $rejection = '<div id="'.$row->id.'" class="reject-operation btn btn-danger reject">Reject</div>';

        } else if($row->status == 1){

          $status = 'Active';
          $action = '<div id="'.$row->id.'" class="activate-operation btn btn-danger De-Activate">De-Activate</div>';
         // $rejection = '<div id="'.$row->id.'" class="reject-operation btn btn-danger reject">Reject</div>';

        } else if($row->status == 2){

          $status = 'Rejected';
          $action = '<div id="'.$row->id.'" class="no-action btn btn-primary">No Action</div>';
          //$rejection = '<div id="'.$row->id.'" class="no-action btn btn-primary">No Action</div>';

        }
        if($user_role[1]=="management" || $user_role[1]=="administrator")
        {
        	$info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$status.'</td><td>'.$country.'</td><td>'.$refername.'</td><td>'.$action.'</td><td><a class="btn btn-success" href="/user/'.$row->id.'/edit">Edit</a></td><td><a class="btn btn-primary" href="/profile/'.$row->id.'/view">View Profile</a></td></tr>';
        }
        else
        {
        	$info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$status.'</td><td>'.$country.'</td><td>'.$refername.'</td><td><a class="btn btn-primary" href="/profile/'.$row->id.'/view">View Profile</a></td></tr>';
        }
           //  $info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$status.'</td><td>'.$action.'</td><td>'.$rejection.'</td><td><a class="btn btn-success" href="/user/'.$row->id.'/edit">Edit</a></td></tr>';

      }

      $info_values .= '</tbody></table></div>';

    } else {

      $info_values = '<h3>No Result Found</h3>';

    }


     
     $ajax_response->addCommand(new HtmlCommand('#offers-informations', $info_values));

     return $ajax_response;
   

  }

}