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
class EditSubCategory extends FormBase {


  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

   // Select.
    $form['links'] = [
      '#theme' => 'item_list',
      '#items' => [
        Link::createFromRoute($this->t('&laquo; Back'), 'category.view_sub_category'),
      ],
    ];

    $cid = \Drupal::routeMatch()->getRawParameter('id');

    $squery = db_select('custom_offers_master','category');
    $squery->fields('category');
    $squery->condition('id',$cid,'=');
    $sresult = $squery->execute();
    $sdata = $sresult->fetchObject();

    $query = db_select('custom_offers_master','category');
    $query->fields('category', ['id', 'name']);
    $query->condition('parent_id', '0','=');
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
      $arrayName[$value->id]=t($value->name);

    }

    $form['parent_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Category Name'),
      '#options' => @$arrayName,
      '#empty_option' => $this->t('-Select Category Name-'),
      '#default_value' => $sdata->parent_id
    ];
    

    // Textfield.
    $form['category_name'] = [
      '#type' => 'textfield',
      '#title' => t('Sub Category Name: '),
      '#size' => 60,
      '#maxlength' => 128,
      '#required' => true,
      '#default_value' => $sdata->name
    ];

    

    // Use the #managed_file FAPI element to upload an image file.
       

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
    return 'edit_sub_category_form';
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

    $cid = \Drupal::routeMatch()->getRawParameter('id');

    $update = db_update('custom_offers_master')->fields($post)->condition('id', $cid, '=')->execute();

    
    if($update>0){
      drupal_set_message("Detail Updated Successfully!");
    }else{
      drupal_set_message(t("Detail not updated."), 'warning');
    }

  }

}
