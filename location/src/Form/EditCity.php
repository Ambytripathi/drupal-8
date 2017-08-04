<?php

namespace Drupal\location\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Link;
/**
 * Implements InputDemo form controller.
 *
 * This example demonstrates the different input elements that are used to
 * collect data in a form.
 */
class EditCity extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Get Data By Id
    $cid = \Drupal::routeMatch()->getRawParameter('id');

    $query = db_select('custom_country','country');
    $query->fields('country', ['id', 'country_name']);
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
      $arrayName[$value->id]=t($value->country_name);
    }

    $qstate = db_select('custom_state','state');
    $qstate->fields('state', ['id', 'state_name']);
    $rstate = $qstate->execute();
    $dstate = $rstate->fetchAll();
    foreach ($dstate as $value) {
      $arrayState[$value->id]=t($value->state_name);
    }

    $squery = db_select('custom_city','city');
    $squery->fields('city');
    $squery->condition('id',$cid,'=');
    $sresult = $squery->execute();
    $sdata = $sresult->fetchObject();

    $qcountry = db_select('custom_state','state');
    $qcountry->fields('state');
    $qcountry->condition('id',$sdata->state_id,'=');
    $rcountry = $qcountry->execute();
    $dcountry = $rcountry->fetchObject();


    
    $form['links'] = [
      '#theme' => 'item_list',
      '#items' => [
        Link::createFromRoute($this->t('&laquo; Back'), 'location.description'),
      ],
    ];

    $form['country_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Country Name'),
      '#options' => @$arrayName,
      '#empty_option' => $this->t('-Select Country Name-'),
      '#ajax' => array(
                        'callback' => '::_ajaxfunction',
                        'wrapper' => 'getstate'
                      ),
      '#default_value' => $dcountry->country_id
    ];




    $form['state_id'] = [
      '#type' => 'select',
      '#title' => $this->t('State Name'),
      '#prefix' => '<div id="getstate">',
      '#suffix' => '</div>',
      '#options' => $arrayState,
      '#empty_option' => $this->t('-Select State Name-'),
      '#default_value' => $sdata->state_id
    ];


    // Textfield.
    $form['city_name'] = [
      '#type' => 'textfield',
      '#title' => t('City Name: '),
      '#size' => 60,
      '#maxlength' => 128,
      '#default_value' => $sdata->city_name
    ];

    $form['city_alias_name'] = [
      '#type' => 'textfield',
      '#title' => t('City Alias Name: '),
      '#size' => 60,
      '#maxlength' => 128,
      '#default_value' => $sdata->city_alias_name
    ];

    // Textarea.
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $sdata->description
    ];

    $form['image'] = array(
      '#type'          => 'managed_file',
      '#title'         => t('Choose Image File'),
      '#upload_location' => 'public://images/',
      '#default_value' => '',
      '#states'        => array(
        'visible'      => array(
          ':input[name="image_type"]' => array('value' => t('Upload New Image(s)')),
        ),
      ),
    );
    

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update'),
      '#description' => $this->t('Save, #type = submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'location_state_form';
  }

  public function _ajaxfunction(array &$form, FormStateInterface $form_state) {
      $cid = $form_state->getValue('country_id');
      $squery = db_select('custom_state','state');
      $squery->fields('state', ['id', 'state_name']);
      $squery->condition('country_id',$cid,'=');
      $sresult = $squery->execute();
      $sdata = $sresult->fetchAll();
      $options_state[''] = t('-Select State Name-');
      foreach ($sdata as $state) {

        $options_state[$state->id] = t($state->state_name);

      }


      $form['state_id'] = [
        '#type' => 'select',
        '#title' => $this->t('State Name'),
        '#prefix' => '<div id="getstate">',
        '#suffix' => '</div>',
        '#options' => $options_state,
        '#name' => 'state_id',
        '#empty_option' => $this->t('-Select State Name-'),
      ];

      $ajax_response = new AjaxResponse();
      $ajax_response->addCommand(new HtmlCommand('#getstate', $form['state_id']));
      //return $options_state;
      return $ajax_response;
   }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $uid = \Drupal::currentUser()->id();
    $image = $form_state->getValue('image');
     $post = array(
        'city_name' => $form_state->getValue('city_name'),
        'city_alias_name' => $form_state->getValue('city_alias_name'),
        'description' => $form_state->getValue('description'),
        'state_id' => $form_state->getValue('state_id'),
        'image' => $image[0],
        'created_by' => $uid,
      );

    $cid = \Drupal::routeMatch()->getRawParameter('id');

    $update = db_update('custom_city')->fields($post)->condition('id', $cid, '=')->execute();

    if($update>0){
    drupal_set_message("City detail updated successfully.");
    }else{
      drupal_set_message(t("Detail not inserted."), 'warning');
    }
    
  }

}
