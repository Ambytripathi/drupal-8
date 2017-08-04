<?php

namespace Drupal\category\Form;

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
class CategoryForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

   // Select.
    $form['links'] = [
      '#theme' => 'item_list',
      '#items' => [
        Link::createFromRoute($this->t('&laquo; Back'), 'category.description'),
      ],
    ];

    // Textfield.
    $form['category_name'] = [
      '#type' => 'textfield',
      '#title' => t('Category Name: '),
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => true
    ];

    
    

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
    $post = array(
        'name' => $form_state->getValue('category_name'),
        'parent_id' => 0,
        'created_by' => $uid,
      );
    $insert = db_insert('custom_offers_master')
    -> fields($post)
    ->execute();

    if($insert>0){
    drupal_set_message("Category inserted successfully.");
    }else{
      drupal_set_message(t("Detail not inserted."), 'warning');
    }

  }

}
