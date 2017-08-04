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
class SubCategoryForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get Result
    $query = db_select('custom_offers_master','category');
    $query->fields('category', ['id', 'name']);
    $query->condition('parent_id', '0','=');
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
      $arrayName[$value->id]= t($value->name);
    }
    
    //print_r($arrayName); exit;

   // Select.
    $form['links'] = [
      '#theme' => 'item_list',
      '#items' => [
        Link::createFromRoute($this->t('&laquo; Back'), 'category.description'),
      ],
    ];

    if(!empty($arrayName)){


    $form['parent_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Category Name'),
      '#options' => $arrayName,
      '#empty_option' => $this->t('-Select category Name-'),
    ];

    // Textfield.
    $form['category_name'] = [
      '#type' => 'textfield',
      '#title' => t('Sub Category Name: '),
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => true
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#description' => $this->t('Save, #type = submit'),
    ];

  }else{
    $form['data'] = [
      '#type' => 'markup',
      '#markup' => t('Main category not available.'),
    ];
  }

    
    

     // Add a submit button that handles the submission of the form.
    

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
        'parent_id' => $form_state->getValue('parent_id'),
        'created_by' => $uid,
      );
    $insert = db_insert('custom_offers_master')
    -> fields($post)
    ->execute();

    if($insert>0){
    drupal_set_message("Sub Category inserted successfully.");
    }else{
      drupal_set_message(t("Detail not inserted."), 'warning');
    }

  }

}
