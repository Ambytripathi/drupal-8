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
class ViewReferEarn extends FormBase {
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
    $uid = \Drupal::currentUser()->id();
    $roles = \Drupal::currentUser()->getRoles();
    // get Roles
    $query = db_select('custom_roles','roles');
    $query->fields('roles', ['target_id', 'name']);
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
      $roleName[$value->target_id]= t($value->name);
    }

    // get name

    $n_query = db_select('custom_users_information','name');
    $n_query->fields('name', ['id','first_name', 'last_name']);
    $n_result = $n_query->execute();
    $n_data = $n_result->fetchAll();
    foreach ($n_data as $value) {
      $nameName[$value->id]= t($value->first_name.' '.$value->last_name);
    }

    // get country
    $co_query = db_select('custom_country','country');
    $co_query->fields('country', ['id', 'country_name']);
    $co_result = $co_query->execute();
    $co_data = $co_result->fetchAll();
    foreach ($co_data as $value) {
      $coName[$value->id]= t($value->country_name);
    }
    // get city
    $ci_query = db_select('custom_city','city');
    $ci_query->fields('city', ['id', 'city_name']);
    $ci_result = $ci_query->execute();
    $ci_data = $ci_result->fetchAll();
    foreach ($ci_data as $value) {
      $ciName[$value->id]= t($value->city_name);
    }
    
    $form['#attached']['library'][] = 'user_details/user-details';
	  
    $form['refer_promoters'] = array(
      '#type' => 'fieldset',
      '#title' => t('Search Filter:'),
    );

   

    $form['refer_promoters']['email'] = array(
      '#type' => 'email',
      '#title' => t('Refered Email:'),
      '#prefix' => '<div id="name-block" class="row"><div class="col-md-3">',
      '#suffix' => '</div>',
      '#required' => TRUE,
    );

    // $form['refer_promoters']['role'] = [
    //   '#type' => 'select',
    //   '#title' => $this->t('Role'),
    //   '#options' => ['local_experts'=>'Local Experts', 'partners'=>'Promoters'],
    //   '#empty_option' => $this->t('All'),
    //   '#prefix' => '<div class="col-md-3">',
    //   '#suffix' => '</div>',
    // ];

    $form['refer_promoters']['role'] = [
      '#type' => 'select',
      '#title' => $this->t('Refered Role'),
      '#options' => ['local_experts'=>'Local Experts'],
      '#empty_option' => $this->t('All'),
      '#prefix' => '<div class="col-md-3">',
      '#suffix' => '</div>',
    ];

    if($roles[1]!='partners' && $roles[1]!='local_experts' && $roles[1]!='diplomats'){

      $form['refer_promoters']['rname'] = [
        '#type' => 'select',
        '#title' => $this->t('Refered By Name'),
        '#options' => $nameName,
        '#empty_option' => $this->t('All'),
        '#prefix' => '<div class="col-md-3">',
        '#suffix' => '</div>',
      ];

      $form['refer_promoters']['rrole'] = [
        '#type' => 'select',
        '#title' => $this->t('Refered By Role'),
        '#options' => $roleName,
        '#empty_option' => $this->t('All'),
        '#prefix' => '<div class="col-md-3">',
        '#suffix' => '</div>',
      ];

      $form['refer_promoters']['country'] = [
        '#type' => 'select',
        '#title' => $this->t('Country'),
        '#options' => $coName,
        '#empty_option' => $this->t('All'),
        '#prefix' => '<div class="col-md-3">',
        '#suffix' => '</div>',
      ];

      $form['refer_promoters']['city'] = [
        '#type' => 'select',
        '#title' => $this->t('City'),
        '#options' => $ciName,
        '#empty_option' => $this->t('All'),
        '#prefix' => '<div class="col-md-3">',
        '#suffix' => '</div>',
      ];
      
    }

    $form['refer_promoters']['status'] = [
      '#type' => 'select',
      '#title' => $this->t('Status'),
      '#options' => ['Pending'=>'Pending', 'Signed Up'=>'Signed Up'],
      '#empty_option' => $this->t('All'),
      '#prefix' => '<div class="col-md-3">',
      '#suffix' => '</div></div>',
    ];

   



    $form['refer_promoters']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Search'),
      '#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r")),
      '#ajax' => array(
                        'callback' => '::_ajaxfunction',
                        'wrapper' => 'getuser',
                        'event' => 'click',
                      ),
    );

    // Table Start
   


    $t_query = db_select('custom_referral','r');
    $t_query->fields('r', ['id','email_id','status','type','created_on']);
    $t_query->fields('cr', ['name']);
    $t_query->fields('cui', ['first_name', 'last_name']);
    $t_query->fields('cc', ['country_name']);
    $t_query->fields('cci', ['city_name']);
    $t_query->leftJoin('custom_users','cu','r.user_id = cu.id');
    $t_query->leftJoin('custom_users_information','cui','cu.id = cui.id');
    $t_query->leftJoin('custom_roles','cr','cu.role = cr.target_id');
    $t_query->leftJoin('custom_country','cc','cui.country = cc.id  OR cui.country=NULL');
    $t_query->leftJoin('custom_city','cci','cui.city = cci.id OR cui.city=NULL');

    if($roles[1]=='partners' || $roles[1]=='diplomats'){
    $t_query->condition('r.user_id',$uid,'=');
    }
    $t_result = $t_query->execute();
    $t_data = $t_result->fetchAll();

    //print_r($t_data);

    $form['view_users'] = array(
      '#type' => 'fieldset',
      '#title' => t('View Refer User:'),
    );

     if($roles[1]!='partners' && $roles[1]!='local_experts' && $roles[1]!='diplomats'){
          $header = array(
              array('data' => $this->t('Refered Email')),
              array('data' => $this->t('Refered Role')),
              array('data' => $this->t('Status')),
              array('data' => $this->t('Refered By Name')),
              array('data' => $this->t('Refered By Role')),
              array('data' => $this->t('Country')),
              array('data' => $this->t('City')),
              array('data' => $this->t('Created Date')),

          );

          $post = array();
           foreach ($t_data as $v) {
            if($v->type=='local_experts'){
              $type = 'Local Experts';
            }else if($v->type=='partners'){
              $type = 'Promoters';
            }
            $date = date('m-d-Y',strtotime($v->created_on));
            $post[] = array(
                      $v->email_id,
                      $type,
                      $v->status,
                      $v->first_name.' '.$v->last_name,
                      $v->name,
                      $v->country_name,
                      $v->city_name,
                      $date);
            }
    }else{

          $header = array(
              array('data' => $this->t('Refered Email')),
              array('data' => $this->t('Refered Role')),
              array('data' => $this->t('Status')),
              array('data' => $this->t('Created Date')),

          );

          $post = array();
           foreach ($t_data as $v) {
            if($v->type=='local_experts'){
              $type = 'Local Experts';
            }else if($v->type=='partners'){
              $type = 'Promoters';
            }
            $date = date('m-d-Y',strtotime($v->created_on));
            $post[] = array(
                      $v->email_id,
                      $type,
                      $v->status,
                      $date);
            }

    }

   


   $form['view_users']['view_table'] = array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $post,
        '#prefix' => '<div id="getuser">',
        '#suffix' => '</div>',
    );
   
    return $form;


  }

  /**
   * {@inheritdoc}
   */
   public function _ajaxfunction(array &$form, FormStateInterface $form_state) {
      $roles = \Drupal::currentUser()->getRoles();
      $name = $form_state->getValue('name');
      $email = $form_state->getValue('email');
      $role = $form_state->getValue('role');
      $status = $form_state->getValue('status');
      $rrole = $form_state->getValue('rrole');
      $rname = $form_state->getValue('rname');
      $country = $form_state->getValue('country');
      $city = $form_state->getValue('city');
      
      $uid = \Drupal::currentUser()->id();

      // $t_query = db_select('custom_referral','referral');
      // $t_query->fields('referral', ['id','email_id','status','type','created_on']);
      // if($roles[1]=='partners' || $roles[1]=='diplomats'){
      // $t_query->condition('user_id',$uid,'=');
      // }
      $t_query = db_select('custom_referral','r');
      $t_query->fields('r', ['id','email_id','status','type','created_on']);
      $t_query->fields('cr', ['name']);
      $t_query->fields('cui', ['first_name', 'last_name']);
      $t_query->fields('cc', ['country_name']);
      $t_query->fields('cci', ['city_name']);
      $t_query->leftJoin('custom_users','cu','r.user_id = cu.id');
      $t_query->leftJoin('custom_users_information','cui','cu.id = cui.id');
      $t_query->leftJoin('custom_roles','cr','cu.role = cr.target_id');
      $t_query->leftJoin('custom_country','cc','cui.country = cc.id  OR cui.country=NULL');
      $t_query->leftJoin('custom_city','cci','cui.city = cci.id OR cui.city=NULL');

      if($roles[1]=='partners' || $roles[1]=='diplomats'){
      $t_query->condition('r.user_id',$uid,'=');
      }

      if($name!=''){
        $t_query->condition('r.name',$name,'=');
      }
      if($email!=''){
        $t_query->condition('r.email_id',$email,'=');
      }
      if($role!=''){
        $t_query->condition('r.type',$role,'=');
      }
      if($status!=''){
        $t_query->condition('r.status',$status,'=');
      }
      if($rrole!=''){
        $t_query->condition('cu.role',$rrole,'=');
      }
      if($rname!=''){
        $t_query->condition('cui.id',$rname,'=');
      }
      if($country!=''){
        $t_query->condition('cc.id',$country,'=');
      }
      if($city!=''){
        $t_query->condition('cci.id',$city,'=');
      }
      $t_result = $t_query->execute();
      $t_data = $t_result->fetchAll();

        if($roles[1]!='partners' && $roles[1]!='local_experts' && $roles[1]!='diplomats'){
            $header = array(
                //array('data' => $this->t('Name')),
                array('data' => $this->t('Refered Email')),
                array('data' => $this->t('Refered Role')),
                array('data' => $this->t('Status')),
                array('data' => $this->t('Refered By Name')),
                array('data' => $this->t('Refered By Role')),
                array('data' => $this->t('Country')),
                array('data' => $this->t('City')),
                array('data' => $this->t('Created Date')),

            );

            $post = array();
             foreach ($t_data as $v) {
              if($v->type=='local_experts'){
                $type = 'Local Experts';
              }else if($v->type=='partners'){
                $type = 'Promoters';
              }
              $date = date('m-d-Y',strtotime($v->created_on));
              $post[] = array(
                        //'',
                        $v->email_id,
                        $type,
                        $v->status,
                        $v->first_name.' '.$v->last_name,
                        $v->name,
                        $v->country_name,
                        $v->city_name,
                        $date);
              }
      }else{

            $header = array(
                array('data' => $this->t('Refered Email')),
                array('data' => $this->t('Refered Role')),
                array('data' => $this->t('Status')),
                array('data' => $this->t('Created Date')),

            );

            $post = array();
             foreach ($t_data as $v) {
              if($v->type=='local_experts'){
                $type = 'Local Experts';
              }else if($v->type=='partners'){
                $type = 'Promoters';
              }
              $date = date('m-d-Y',strtotime($v->created_on));
              $post[] = array(
                        $v->email_id,
                        $type,
                        $v->status,
                        $date);
              }

      }

     $form['view_users']['view_table'] = array(
          '#theme' => 'table',
          '#header' => $header,
          '#rows' => $post,
      );

 
      $ajax_response = new AjaxResponse();
      $ajax_response->addCommand(new HtmlCommand('#getuser', $form['view_users']['view_table']));
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
