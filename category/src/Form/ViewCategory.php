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
class ViewCategory extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

   // Select.
    $content['links'] = [
      '#theme' => 'item_list',
      '#items' => [
        Link::createFromRoute($this->t('&laquo; Back'), 'category.description'),
      ],
    ];

    $header = array(
      array('data' => $this->t('ID')),
      array('data' => $this->t('Category Name')),
      array('data' => $this->t('Action')),
      array('data' => $this->t('')),
    );

    //Create a list of links to the form examples.
    $query = db_select('custom_offers_master','category');
    $query->fields('category', ['id', 'name']);
    $query->condition('parent_id', '0','=');
    $result = $query->execute();
    $data = $result->fetchAll();
    $i = 0;


    foreach ($data as $value) {

       $post[] =
            array(
              $value->id,
              $value->name,
              Link::createFromRoute($this->t('Edit'), 'category.edit_category', array('id'=>$value->id)),
              Link::createFromRoute($this->t('Delete'), 'category.delete_category', array('id'=>$value->id)),
            );

       $i++;
    }

    // echo "<pre>";
    // print_r($post); exit;
    
 

     // Generate the table.
      $content['config_table'] = array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $post,
      );

    // The message container is used by the modal form example. It is an empty
    // tag that will be replaced by content.
    $content['message'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'location-message'],
    ];
    return $content;

  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'view_category_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get Currnt User Detail
    
  }

}
