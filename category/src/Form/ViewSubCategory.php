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
class ViewSubCategory extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

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
      '#title' => $this->t('Country Name'),
      '#options' => $arrayName,
      '#empty_option' => $this->t('-Select Category Name-'),
      '#ajax' => array(
                        'callback' => '::_ajaxfunction',
                        'wrapper' => 'getstate'
                      ),
    ];

    $form['data'] = [
      '#type' => 'markup',
      '#markup' => t('Please select category name first.'),
      '#prefix' => '<div id="getstate">',
      '#suffix' => '</div>',
    ];

    }else{
      $form['data'] = [
        '#type' => 'markup',
        '#markup' => t('Main category not available.'),
      ];
    }
   
    
    
    return $form;

    
   }

   /**
   * {@inheritdoc}
   */
   public function _ajaxfunction(array &$form, FormStateInterface $form_state) {
      $cid = $form_state->getValue('parent_id');

      $query = db_select('custom_offers_master','category');
      $query->fields('category', ['id', 'name']);
      $query->condition('parent_id', $cid,'=');
      $result = $query->execute();
      $data = $result->fetchAll();
      foreach ($data as $value) {
        $arrayName[$value->id]= t($value->name);
      }

      $header = array(
        array('data' => $this->t('ID')),
        array('data' => $this->t('Sub Category Name')),
        array('data' => $this->t('Action')),
        array('data' => $this->t('')),
      );

      $i = 0;


      foreach ($data as $value) {

         $post[] =
              array(
                $value->id,
                $value->name,
                Link::createFromRoute($this->t('Edit'), 'category.edit_sub_category', array('id'=>$value->id)),
                Link::createFromRoute($this->t('Delete'), 'category.delete_category', array('id'=>$value->id)),
              );

         $i++;
      }

    
   

       // Generate the table.
        $form['config_table'] = array(
          '#theme' => 'table',
          '#header' => $header,
          '#rows' => $post,
        );

      $ajax_response = new AjaxResponse();
      $ajax_response->addCommand(new HtmlCommand('#getstate', $form['config_table']));
      return $ajax_response;
   }
   

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'view_sub_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
  }

}
