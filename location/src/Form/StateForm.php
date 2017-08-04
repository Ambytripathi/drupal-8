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
class StateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    

    // Get Result
    $query = db_select('custom_country','country');
    $query->fields('country', ['id', 'country_name']);
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
      $arrayName[$value->id]=t($value->country_name);

    }

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
    ];
  

    // Textfield.
    $form['state_name'] = [
      '#type' => 'textfield',
      '#title' => t('State Name: '),
      '#size' => 60,
      '#maxlength' => 128,
    ];

    $form['state_alias_name'] = [
      '#type' => 'textfield',
      '#title' => t('State Alias Name: '),
      '#size' => 60,
      '#maxlength' => 128,
    ];

    // Textarea.
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
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
      '#value' => $this->t('Save'),
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

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $uid = \Drupal::currentUser()->id();
    $image = $form_state->getValue('image');
    $post = array(
        'state_name' => $form_state->getValue('state_name'),
        'state_alias_name' => $form_state->getValue('state_alias_name'),
        'description' => $form_state->getValue('description'),
        'country_id' => $form_state->getValue('country_id'),
        'image' => @$image[0],
        'created_by' => $uid,
      );
    $insert = db_insert('custom_state')
    -> fields($post)
    ->execute();

    if($insert>0){
    drupal_set_message("State detail inserted successfully.");
    }else{
      drupal_set_message(t("Detail not inserted."), 'warning');
    }
    
  }

}
