<?php

namespace Drupal\location\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Link;
/**
 * Implements InputDemo form controller.
 *
 * This example demonstrates the different input elements that are used to
 * collect data in a form.
 */
class CountryForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

   // Select.
    $form['links'] = [
      '#theme' => 'item_list',
      '#items' => [
        Link::createFromRoute($this->t('&laquo; Back'), 'location.description'),
      ],
    ];

    // Textfield.
    $form['country_name'] = [
      '#type' => 'textfield',
      '#title' => t('Country Name: '),
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => true
    ];

    $form['country_code'] = [
      '#type' => 'textfield',
      '#title' => t('Country Code: '),
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => true
    ];
    $form['country_phone_code'] = [
      '#type' => 'textfield',
      '#title' => t('Country Phone Code: '),
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => true
    ];
   // Textarea.
    $form['description'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#required' => true
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

    // Use the #managed_file FAPI element to upload an image file.
       

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
    return 'location_country_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get Currnt User Detail
    $uid = \Drupal::currentUser()->id();
    $image = $form_state->getValue('image');
    $post = array(
        'country_name' => $form_state->getValue('country_name'),
        'country_code' => $form_state->getValue('country_code'),
        'phone_code' => $form_state->getValue('country_phone_code'),
        'description' => $form_state->getValue('description'),
        'image' => @$image[0],
        'created_by' => $uid,
      );
    $insert = db_insert('custom_country')
    -> fields($post)
    ->execute();

    if($insert>0){
    drupal_set_message("Country detail inserted successfully.");
    }else{
      drupal_set_message(t("Detail not inserted."), 'warning');
    }

  }

}
