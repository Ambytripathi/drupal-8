<?php

namespace Drupal\nida_offers\Form;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ChangedCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\nida_offers\Controller\OffersController;
use Drupal\Core\Url;

class OffersForm extends FormBase {

  public function getFormId() {
     
    return 'offers_creation_form';
  
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    //$offer_id = $_GET['id'];

    if(isset($_SESSION['new_offer_id'])){ $offer_id = $_SESSION['new_offer_id']; }

    if($offer_id > 0){

      $query = db_select('custom_offers','cc');
      $query->fields('cc', ['id', 'name','category','sub_category','country','city','teaser','description',"country_id","city_id"]);
      $query->condition('id',$offer_id);
      $result = $query->execute();
      $data = $result->fetchAll();
      foreach ($data as $datas) { }
    
    }

        $cid = $datas->country_id;
        $query_result = db_select('custom_state','cc');
        $query_result->fields('cc', ["id","state_name"]);
        if($cid > 0){ $query_result->condition('country_id',$cid); }
        $result = $query_result->execute();
        $data = $result->fetchAll();
        foreach($data as $row){
            $sid = $row->id;
            $query = db_select('custom_city','cc');
            $query->fields('cc', ["id","city_name"]);
            $query->condition('state_id',$sid);
            $result = $query->execute();
            $dataer = $result->fetchAll();
            foreach ($dataer as $key => $value) {
                $city_array[$value->id] =$value->city_name;
            }

        }

    

    $query = db_select('custom_country','cc');
    $query->fields('cc', ['id', 'country_name','description','country_code']);
    $result = $query->execute();
    $data = $result->fetchAll();
    $cntry_name = array();
    foreach ($data as $value) {
      $cntry_name[$value->id] = $value->country_name;
    }

    $query = db_select('custom_offers_master','cc');
    $query->fields('cc', ['id', 'name','parent_id']);
    $query->condition('parent_id','0');
    $result = $query->execute();
    $data = $result->fetchAll();
    $category_name = array();
    foreach ($data as $value) {
      $category_name[$value->id] = $value->name;
    }

    $query = db_select('custom_offers_master','cc');
    $query->fields('cc', ['id', 'name','parent_id']);
    $query->condition('parent_id','0','>');
    $result = $query->execute();
    $data = $result->fetchAll();
    $scategory_name = array();
    foreach ($data as $value) {
      $scategory_name[$value->id] = $value->name;
    }

    $form['offer_id'] = array(
      '#type' => 'hidden',
      '#default_value' => $offer_id ? $offer_id : '',
    );

    $form['country'] = array(
      '#type' => 'select',
      '#title' => t('Country'),
      '#prefix' => '<div class="row"><div class="col-xs-6">',
      '#suffix' => '</div>', 
      '#options' => $cntry_name,
      '#required' => TRUE,
      '#empty_option' => t('Select'),
      '#default_value' => $datas->country_id ? $datas->country_id : '',
    );

    $form['city'] = array(
      '#type' => 'select',
      '#title' => t('City'),
      '#options' => $city_array,
      '#required' => TRUE,
      '#validated' => TRUE,
      '#prefix' => '<div class="col-xs-6">',
      '#suffix' => '</div></div>', 
      '#empty_option' => t('Select'),
      '#default_value' => $datas->city_id ? $datas->city_id : ''
    );

    $form['category'] = array(
      '#type' => 'select',
      '#title' => t('Category'),
      '#options' => $category_name,
      '#required' => TRUE,
      '#empty_option' => t('Select'),
      '#default_value' => $datas->category ? $datas->category : ''
    );

    $form['sub_category'] = array(
      '#type' => 'select',
      '#title' => t('Sub Category'),
      '#options' => $scategory_name,
      '#validated' => TRUE,
      '#required' => TRUE,
      '#empty_option' => t('Select'),
      '#default_value' => $datas->sub_category ? $datas->sub_category : ''
    );

    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#required' => TRUE,
      '#default_value' => $datas->name ? $datas->name : ''
    );

    $form['teaser'] = array(
      '#type' => 'textfield',
      '#title' => t('Teaser'),
      '#required' => TRUE,
      '#default_value' => $datas->teaser ? $datas->teaser : ''
    );

    $form['description'] = array(
      '#type' => 'text_format',
      '#title' => t('Description'),
      '#required' => TRUE,
      '#format' => 'full_html',
      '#default_value' => $datas->description ? $datas->description : '' 
    );
                    
    /*$form['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Save'),
      '#attributes' => array("class"=>array(" btn btn-success w-150 m-sm-r")),
    );*/

    $form['next'] = array(
      '#type' => 'submit',
      '#value' => t('Next'),
      '#submit' => array("::nextsubmitForm"),
      '#attributes' => array("class"=>array(" btn btn-primary w-150 m-sm-r")),
    );    

    return $form;
  
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {

   
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {

    $name = $form_state->getValue('name');
    $alias_name = $form_state->getValue('alias_name');
    $category = $form_state->getValue('category');
    $sub_category = $form_state->getValue('sub_category');
    $description = $form_state->getValue('description');
    $teaser = $form_state->getValue('teaser');
    $country = $form_state->getValue('country');
    $offer_id = $form_state->getValue('offer_id');
    $city = $form_state->getValue('city');
    $uid = \Drupal::currentUser()->id();

    if($offer_id > 0){

      $inputarray = array("id"=>$offer_id,"name"=>$name,"alias_name"=>$alias_name,"category"=>$category,"sub_category"=>$sub_category,"description"=>$description,"teaser"=>$teaser,"country_id"=>$country,"city_id"=>$city,"expert_id"=>$uid,"created_by"=>$uid,"updated_by"=>$uid);

    } else {

      $inputarray = array("type"=>"new","name"=>$name,"alias_name"=>$alias_name,"category"=>$category,"sub_category"=>$sub_category,"description"=>$description,"teaser"=>$teaser,"country_id"=>$country,"city_id"=>$city,"expert_id"=>$uid,"created_by"=>$uid,"updated_by"=>$uid);

    }

    $classobj = new OffersController();

    $result = $classobj->offers_update($inputarray);

    if($result == 1){ drupal_set_message(t('Experience information saved successfully')); } else { drupal_set_message(t("Failed"),"error"); }

  }

  public function nextsubmitForm(array &$form, FormStateInterface $form_state) {

    $name = $form_state->getValue('name');
    $alias_name = $form_state->getValue('alias_name');
    $category = $form_state->getValue('category');
    $sub_category = $form_state->getValue('sub_category');
    $description = $form_state->getValue('description');
    $teaser = $form_state->getValue('teaser');
    $country = $form_state->getValue('country');
    $offer_id = $form_state->getValue('offer_id');
    $city = $form_state->getValue('city');
    $uid = \Drupal::currentUser()->id();

    if($offer_id > 0){

      $inputarray = array("id"=>$offer_id,"name"=>$name,"alias_name"=>$alias_name,"category"=>$category,"sub_category"=>$sub_category,"description"=>$description,"teaser"=>$teaser,"country_id"=>$country,"city_id"=>$city,"expert_id"=>$uid,"created_by"=>$uid,"updated_by"=>$uid);

    } else {

      $inputarray = array("type"=>"new","name"=>$name,"alias_name"=>$alias_name,"category"=>$category,"sub_category"=>$sub_category,"description"=>$description,"teaser"=>$teaser,"country_id"=>$country,"city_id"=>$city,"expert_id"=>$uid,"created_by"=>$uid,"updated_by"=>$uid);

    }

    $classobj = new OffersController();

    $result = $classobj->offers_update($inputarray);

    if($result == 1){

        if(isset($_SESSION['offer_id']) && $_SESSION['offer_id'] > 0){
          $offer_id = $_SESSION['offer_id'];
          $redirect_path_next = "/profile/experience-schedule";
          $url_next = url::fromUserInput($redirect_path_next);
          $form_state->setRedirectUrl($url_next);
        } else {

        drupal_set_message(t('Experience information saved successfully'));

        }

    } else {

        drupal_set_message(t("Failed"),"error");

    }

  }



}
?>
