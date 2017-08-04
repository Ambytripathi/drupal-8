<?php

namespace Drupal\user_details\Form;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ChangedCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ProfileManagementForm extends FormBase {

  public function getFormId() {
    
    return 'profile_management_form';
  
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
  	$user_role = \Drupal::currentUser()->getRoles();
    $info_values = '';

  	$info_values .= '<div class="table-responsive"><table class="table table-hover table-striped"><thead>
    <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Roles</th><th>Editor Approval</th><th>Management Approval</th><th>Status</th><th>Actions</th><th>Reject</th><th>View Full Profile</th></tr></thead><tbody>';
    
    $query_result = db_select('custom_users','c');
    $query_result->fields('c', ["id","email","status","role","user_id"]);
    $query_result->condition(
    		db_or()
    		->condition('c.role', "%" . $query_result->escapeLike('diplomats') . "%", 'LIKE')
    		->condition('c.role', "%" . $query_result->escapeLike('partners') . "%", 'LIKE')
    		);
    $query_result->join('custom_users_information', 'cc', 'cc.user_id = c.user_id');
    $query_result->fields('cc', ['first_name','last_name','refer_management_approval','refer_editor_approval']);
    
    //$query_result->condition('cc.refer_editor_approval', '1');
    $query_result->orderBy("id","DESC");
    $result = $query_result->execute();
    $data = $result->fetchAll();

    foreach($data as $row){

      $roles = $row->role;
      $editor_approval=$row->refer_editor_approval;
      $mgmt_approval=$row->refer_management_approval;
      if($editor_approval=="1")
      {
      	$editor_approval="Yes";
      }
      else
      {
      	$editor_approval="No";
      }
      if($mgmt_approval=="1")
      {
      	$mgmt_approval="Yes";
      }
      else
      {
      	$mgmt_approval="No";
      }
      if($row->refer_management_approval== 0){

        $status = 'Pending';
        $action = '<div id="'.$row->id.'" class="refer-activation-operation btn btn-success profile_activate ">Activate Refer & Earn</div>';
        $rejection = '<div id="'.$row->id.'" class="refer-reject-operation btn btn-danger profile_reject">Reject</div>';

      } else if($row->refer_management_approval== 1){

        $status = 'Activated';
        $action = '<div id="'.$row->id.'" class="refer-deactivation-operation btn btn-danger profile_deactivate ">De-Activate Refer & Earn</div>';
        $rejection = '<div id="'.$row->id.'" class="no-action btn btn-primary">No Action</div>';

      } else if($row->refer_management_approval== 2){

        $status = 'Rejected';
        $action = '<div id="'.$row->id.'" class="refer-activation-operation btn btn-success profile_activate ">Activate Refer & Earn</div>';
        $rejection = '<div id="'.$row->id.'" class="no-action btn btn-primary">No Action</div>';

      }
      else if($row->refer_management_approval== 3){
      	
      	$status = 'Deactivated';
      	$action = '<div id="'.$row->id.'" class="refer-activation-operation btn btn-success profile_activate ">Activate Refer & Earn</div>';
      	$rejection = '<div id="'.$row->id.'" class="no-action btn btn-primary">No Action</div>';
      	
      }
    
      	$info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$editor_approval.'</td><td>'.$mgmt_approval.'</td><td>'.$status.'</td><td>'.$action.'</td><td>'.$rejection.'</td><td><a class="btn btn-primary" href="/profile/'.$row->id.'/view">View Profile</a></td></tr>';
     
      //$info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$status.'</td><td>'.$action.'</td><td>'.$rejection.'</td><td><a class="btn btn-success" href="/user/'.$row->id.'/edit">Edit</a></td></tr>';

    }

    $info_values .= '</tbody></table></div>';

    $form['user_id'] = array(
      '#type' => 'textfield',
      '#title' => t('User ID'),
      '#default_value' => '',
    );
    
    $form['email'] = array(
      '#type' => 'textfield',
      '#title' => t('Email'),
      '#default_value' => '',
    );

    $form['status'] = array(
      '#type' => 'select',
      '#title' => t('status'),
      '#empty_option' => t("All"),
    		'#options' => array("0"=>"Pending","1"=>"Activated","2"=>"rejected","3"=>"De-activated"),
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
      '#suffix' => '<div id="profile-informations">'.$info_values.'</div>'
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

    $info_values = '';
   		$info_values .= '<div class="table-responsive"><table class="table table-hover table-striped"><thead>
    <tr><th>ID</th><th>First Name</th><th>Last Name</th><th>Email</th><th>Roles</th><th>Editor Approval</th><th>Management Approval</th><th>Status</th><th>Actions</th><th>Reject</th><th>View Full Profile</th></tr></thead><tbody>';
   
    $query_result = db_select('custom_users','c');
    $query_result->fields('c', ["id","email","status","role","user_id"]);
    $query_result->condition(
    		db_or()
    		->condition('c.role', "%" . $query_result->escapeLike('diplomats') . "%", 'LIKE')
    		->condition('c.role', "%" . $query_result->escapeLike('partners') . "%", 'LIKE')
    		);
    $query_result->join('custom_users_information', 'cc', 'cc.user_id = c.user_id');
    $query_result->fields('cc', ['first_name','last_name','refer_management_approval','refer_editor_approval']);
    if(isset($status) && $status!=""){ $query_result->condition("cc.refer_management_approval",$status); }
    if(isset($user_id) && $user_id > 2){ $query_result->condition("c.id",$user_id); }
    if(isset($email) && strlen($email) > 2){ $query_result->condition("c.email",$email); }
   //	$query_result->condition('cc.refer_editor_approval', '1');
   
    $query_result->orderBy("c.id","DESC");
    $result = $query_result->execute();
    $data = $result->fetchAll();

    if(count($data) > 0){

      foreach($data as $row){

        $roles = $row->role;
        $editor_approval=$row->refer_editor_approval;
        $mgmt_approval=$row->refer_management_approval;
        if($editor_approval=="1")
        {
        	$editor_approval="Yes";
        }
        else
        {
        	$editor_approval="No";
        }
        if($mgmt_approval=="1")
        {
        	$mgmt_approval="Yes";
        }
        else
        {
        	$mgmt_approval="No";
        }
        if($row->refer_management_approval== 0){
        	
        	$status = 'Pending';
        	$action = '<div id="'.$row->id.'" class="refer-activation-operation btn btn-success profile_activate ">Activate Refer & Earn</div>';
        	$rejection = '<div id="'.$row->id.'" class="refer-reject-operation btn btn-danger profile_reject">Reject</div>';
        	
        } else if($row->refer_management_approval== 1){
        	
        	$status = 'Activated';
        	$action = '<div id="'.$row->id.'" class="refer-deactivation-operation btn btn-danger profile_deactivate ">De-Activate Refer & Earn</div>';
        	$rejection = '<div id="'.$row->id.'" class="no-action btn btn-primary">No Action</div>';
        	
        } else if($row->refer_management_approval== 2){
        	
        	$status = 'Rejected';
        	$action = '<div id="'.$row->id.'" class="refer-activation-operation btn btn-success profile_activate ">Activate Refer & Earn</div>';
        	$rejection = '<div id="'.$row->id.'" class="no-action btn btn-primary">No Action</div>';
        	
        }
        else if($row->refer_management_approval== 3){
        	
        	$status = 'Deactivated';
        	$action = '<div id="'.$row->id.'" class="refer-activation-operation btn btn-success profile_activate ">Activate Refer & Earn</div>';
        	$rejection = '<div id="'.$row->id.'" class="no-action btn btn-primary">No Action</div>';
        	
        }
        //if($user_role[1]=="management" || $user_role[1]=="administrator")
        //{
        	$info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$editor_approval.'</td><td>'.$mgmt_approval.'</td><td>'.$status.'</td><td>'.$action.'</td><td>'.$rejection.'</td><td><a class="btn btn-primary" href="/profile/'.$row->id.'/view">View Profile</a></td></tr>';
        //}
        //else
        //{
        	//$info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$status.'</td><td>'.$action.'</td><td>'.$rejection.'</td><td><a class="btn btn-primary" href="/user/'.$row->id.'/view">View Profile</a></td></tr>';
        //}
           //  $info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->first_name.'</td><td>'.$row->last_name.'</td><td>'.$row->email.'</td><td>'.$roles.'</td><td>'.$status.'</td><td>'.$action.'</td><td>'.$rejection.'</td><td><a class="btn btn-success" href="/user/'.$row->id.'/edit">Edit</a></td></tr>';

      }

      $info_values .= '</tbody></table></div>';

    } else {

      $info_values = '<h3>No Result Found</h3>';

    }


     
     $ajax_response->addCommand(new HtmlCommand('#profile-informations', $info_values));

     return $ajax_response;
   

  }

}