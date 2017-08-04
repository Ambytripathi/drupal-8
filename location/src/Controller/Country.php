<?php

namespace Drupal\location\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;

/**
 * Simple page controller for drupal.
 */
class Country extends ControllerBase {

  /**
   * Lists the examples provided by form_example.
   */
  public function view() {
     // These libraries are required to facilitate the ajax modal form demo.
    $content['#attached']['library'][] = 'core/drupal.ajax';
    $content['#attached']['library'][] = 'core/drupal.dialog';
    $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
   
    $header = array(
      array('data' => $this->t('ID')),
      array('data' => $this->t('Country Code')),
      array('data' => $this->t('Country Phone Code')),
      array('data' => $this->t('Country Name')),
      array('data' => $this->t('Description')),
      array('data' => $this->t('Action')),
    );

    //Create a list of links to the form examples.
    $content['links'] = [
      '#theme' => 'item_list',
      '#items' => [
        Link::createFromRoute($this->t('&laquo; Back'), 'location.description'),
      ],
    ];

    //Create a list of links to the form examples.
    $query = db_select('custom_country','country');
    $query->fields('country', ['id', 'country_name','description','country_code','phone_code']);
    $result = $query->execute();
    $data = $result->fetchAll();
    $i = 0;


    foreach ($data as $value) {

       $post[] =
            array(
              $value->id,
              $value->country_code,
              $value->phone_code,
              $value->country_name,
              $value->description,
              Link::createFromRoute($this->t('Edit'), 'location.edit_country', array('id'=>$value->id)),
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

}
