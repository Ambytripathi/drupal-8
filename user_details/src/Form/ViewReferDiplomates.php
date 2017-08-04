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
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Link;


/**
 * Payment form.
 */
class ViewReferDiplomates extends FormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
	   
    return 'user_details_view_refer_earn';
  
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    
    $info_values = '';

    $uid = \Drupal::currentUser()->id();

    $info_values .= '<div class="table-responsive"><table class="table table-hover table-striped"><thead>
    <tr><th>ID</th><th>Experience Name</th><th>Country</th><th>City</th><th>Status</th><th>Created on</th><th>View</th></tr></thead><tbody>';
    // $query_result = db_select('custom_users','cu');
    // $query_result->leftJoin('custom_offers','co','cu.id=co.created_by');
    // $query_result->fields('co', ["id","name","status","created_on","editor_approval","management_approval","country","city"]);
    // $query_result->condition('co.created_by', '1');
    // $query_result->orderBy("co.id","DESC");
    // $result = $query_result->execute();
    // $data = $result->fetchAll();

    $query_result = db_select('custom_users','cu');
    $query_result->condition('cu.parent_id',$uid);
    $query_result->leftJoin('custom_offers','co','cu.id=co.created_by');
    $query_result->fields('co', ["id","name","status","created_on","editor_approval","management_approval","country","city"]);
    $query_result->orderBy("co.id","DESC");
    $result = $query_result->execute();
    $data = $result->fetchAll();
    

    foreach($data as $row){
      
      $newDate = date("d M Y", strtotime($row->created_on));

      if($row->status == "Pending"){ $action = '<a href="/profile/experience/'.$row->id.'/edit" class="btn btn-success">Edit</a>'; } else { $action = ''; }

      if($row->management_approval == "Yes"){

        $approval_button = '<span id="'.$row->id.'" class="btn btn-danger management-deactivate">De-Activate</span>';
      
      } else{
      
        $approval_button = '<span id="'.$row->id.'" class="btn btn-success management-activate">Activate</span>';
      
      }
      
      $info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->name.'</td><td>'.$row->country.'</td><td>'.$row->city.'</td><td>'.$row->status.'</td><td>'.$newDate.'</td><td><a href="/profile/experiences/'.$row->id.'" class="btn btn-success">View</a></td></tr>';

    }

    $info_values .= '</tbody></table></div>';
    
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#default_value' => '',
      '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
      '#suffix' => '</div>',
    );

    $form['status'] = array(
      '#type' => 'select',
      '#title' => t('status'),
      '#empty_option' => t("Select"),
      '#options' => array("Pending"=>"Pending","Confirmed"=>"Confirmed","Rejected"=>"Rejected"),
      '#prefix' => '<div class="col-md-6">',
      '#suffix' => '</div></div>',
    );
    
    $form['search_submit'] = array(
      '#type' => 'button',
      '#value' => $this->t('Find'),
      '#button_type' => 'success',
      '#ajax' => array(
          'callback' => '::_offersfiltersubmit',
          'effect' => 'fade',
          'progress' => array(
          'type' => 'throbber',
          'message' => "Searching",
          'wrapper' => 'offers-informations',
          ),
      ),
    );

    $form['reset'] = array(
      '#type' => 'button',
      '#value' => $this->t('Reset'),
      '#button_type' => 'default',
      '#suffix' => '<div id="offers-informations">'.$info_values.'</div>'
    );

   
    return $form;


  }

  /**
   * {@inheritdoc}
   */
   public function _offersfiltersubmit(array &$form, FormStateInterface $form_state) {
    
    $uid = \Drupal::currentUser()->id();

    $name = $form_state->getValue("name");
    $status = $form_state->getValue("status");

    $info_values = '';

    $info_values .= '<div class="table-responsive"><table class="table table-hover table-striped"><thead>
    <tr><th>ID</th><th>Experience Name</th><th>Country</th><th>City</th><th>Status</th><th>Created on</th><th>View</th></tr></thead><tbody>';

    
    

    $query_result = db_select('custom_users','cu');
    $query_result->condition('cu.parent_id',$uid);
    $query_result->leftJoin('custom_offers','co','cu.id=co.created_by');
    $query_result->fields('co', ["id","name","status","created_on","editor_approval","management_approval","country","city"]);
    if(isset($name) && strlen($name) > 2){ $query_result->condition("co.name",$name); }
    if(isset($status) && strlen($status) > 2){ $query_result->condition("co.status",$status); }
    $query_result->orderBy("co.id","DESC");
    $result = $query_result->execute();
    $data = $result->fetchAll();
    if(count($data) > 0){

      foreach($data as $row){
        
        $newDate = date("d M Y", strtotime($row->created_on));

        if($row->status == "Pending"){ $action = '<a href="/profile/experience/'.$row->id.'/edit" class="btn btn-success">Edit</a>'; } else { $action = ''; }

        if($row->management_approval == "Yes"){

          $approval_button = '<span id="'.$row->id.'" class="btn btn-danger management-deactivate">De-Activate</span>';
        
        } else{
        
          $approval_button = '<span id="'.$row->id.'" class="btn btn-success management-activate">Activate</span>';
        
        }
        
      $info_values .= '<tr><td>'.$row->id.'</td><td>'.$row->name.'</td><td>'.$row->country.'</td><td>'.$row->city.'</td><td>'.$row->status.'</td><td>'.$newDate.'</td><td><a href="/profile/experiences/'.$row->id.'" class="btn btn-success">View</a></td></tr>';

      }

      $info_values .= '</tbody></table></div>';

    } else {

      $info_values = '<h3>No Result Found</h3>';

    }


     $ajax_response = new AjaxResponse();

     $ajax_response->addCommand(new HtmlCommand('#offers-informations', $info_values));

     return $ajax_response;
   
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
	  
  } 
 
   
   
}
?>
