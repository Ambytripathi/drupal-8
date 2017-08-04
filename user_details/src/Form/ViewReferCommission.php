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
class ViewReferCommission extends FormBase {
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
    $roles = \Drupal::currentUser()->getRoles();


    $info_values .= '<div class="table-responsive"><table class="table table-hover table-striped"><thead>
    <tr><th>S.No.</th><th>Date</th><th>Percentage</th><th>Amount</th></tr></thead><tbody>';
    
    if($roles=='local_experts'){

      $query_result = db_select('custom_bookings','cb');
      $query_result->condition('cb.created_by',$uid);
      $query_result->leftJoin('custom_booking_payment','cbp','cb.id=cbp.booking_id');
      $query_result->fields('cbp', ["amount_paid","created_on"]);
      $query_result->orderBy("cb.id","ASC");
      $result = $query_result->execute();
      $data = $result->fetchAll();

      $i = 0;

      foreach ($data as $v) {
        $no = ++$i;
        $amount = ($v->amount_paid*65)/100;
        $info_values .= '<tr><td>'.$no.'</td><td>'.$v->created_on.'</td><td>65%</td><td>'.$amount.'</td></tr>';
      }
      

    }else if($roles=='partners'){

      $query_first = db_select('custom_user','cu');
      $query_first->condition('cu.parent_id',$uid);
      $query_first->condition('cu.role','partner');
      $query_first->leftJoin('custom_bookings','cb','cu.id=cb.created_by');
      $query_first->leftJoin('custom_booking_payment','cbp','cb.id=cbp.booking_id');
      $query_first->fields('cbp', ["amount_paid","created_on"]);
      $query_first->orderBy("cb.id","ASC");
      $result_first = $result_first->execute();
      $data_first = $result_first->fetchAll();
      
      $i = 0;

      foreach ($data_first as $v) {
        $no = ++$i;
        $amount = ($v->amount_paid*3)/100;
        $info_values .= '<tr><td>'.$no.'</td><td>'.$v->created_on.'</td><td>3%</td><td>'.$amount.'</td></tr>';
      }

     // $query_first = db_select('custom_user','cu');
     

      $i = 0;

      $query_result = db_select('custom_bookings','cb');
      $query_result->condition('cb.created_by',$uid);
      $query_result->leftJoin('custom_booking_payment','cbp','cb.id=cbp.booking_id');
      $query_result->leftJoin('custom_user','cu','cb.created_by=cu.id');
      $query_result->leftJoin('custom_user','cur','cu.parent_id=cur.id');
      $query_result->fields('cur', ["role"]);
      $query_result->fields('cbp', ["amount_paid","created_on"]);
      $query_result->orderBy("cb.id","ASC");
      $result = $query_result->execute();
      $data = $result->fetchAll();

      foreach ($data as $v) {
        $no = ++$i;
        if($v->role == 'diplomats'){
          $per = 10;
          $amount = ($v->amount_paid*10)/100;
        }else{
          $per = 7;
          $amount = ($v->amount_paid*7)/100;
        }
        $info_values .= '<tr><td>'.$no.'</td><td>'.$v->created_on.'</td><td>'.$per.'%</td><td>'.$amount.'</td></tr>';
      }

    }else if($roles=='diplomats'){
        $query_result = db_select('custom_user','cu');
        $query_result->condition('cu.parent_id',$uid);
        $query_result->leftJoin('custom_bookings','cb','cu.id=cb.created_by');
        $query_result->leftJoin('custom_booking_payment','cbp','cb.id=cbp.booking_id');
        $query_result->fields('cbp', ["amount_paid","created_on"]);
        $query_result->orderBy("cb.id","ASC");
        $result = $query_result->execute();
        $data = $result->fetchAll();

        $i = 0;

        foreach ($data as $v) {
          $no = ++$i;
          $amount = ($v->amount_paid*3)/100;
          $info_values .= '<tr><td>'.$no.'</td><td>'.$v->created_on.'</td><td>3%</td><td>'.$amount.'</td></tr>';
        }
    }

    
    //$info_values .= '<tr><td colspan="3">Total</td><td>&nbsp;</td></tr>';

    $info_values .= '</tbody></table></div>';
    
    // $form['from'] = array(
    //   '#type' => 'textfield',
    //   '#title' => t('From'),
    //   '#default_value' => '',
    //   '#attributes' => array('id' => 'datepicker'),
    //   '#prefix' => '<div id="name-block" class="row"><div class="col-md-6">',
    //   '#suffix' => '</div>',
    // );

    // $form['to'] = array(
    //   '#type' => 'textfield',
    //   '#title' => t('To'),
    //   '#default_value' => '',
    //   '#attributes' => array('id' => 'datepicker'),
    //   '#prefix' => '<div class="col-md-6">',
    //   '#suffix' => '</div></div>',
    // );
    
    // $form['search_submit'] = array(
    //   '#type' => 'button',
    //   '#value' => $this->t('Find'),
    //   '#button_type' => 'success',
    //   '#ajax' => array(
    //       'callback' => '::_offersfiltersubmit',
    //       'effect' => 'fade',
    //       'progress' => array(
    //       'type' => 'throbber',
    //       'message' => "Searching",
    //       'wrapper' => 'offers-informations',
    //       ),
    //   ),
    // );

    // $form['reset'] = array(
    //   '#type' => 'button',
    //   '#value' => $this->t('Reset'),
    //   '#button_type' => 'default',
    //   '#suffix' => '<div id="offers-informations">'.$info_values.'</div>'
    // );

    $form['reset'] = array(
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
    <tr><th>S.No.</th><th>Date</th><th>Percentage</th><th>Amount</th></tr></thead><tbody>';
    

    $info_values .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
    $info_values .= '<tr><td colspan="3">Total</td><td>&nbsp;</td></tr>';

    $info_values .= '</tbody></table></div>';

    // } else {

    //   $info_values = '<h3>No Result Found</h3>';

    // }


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
