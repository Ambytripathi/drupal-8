<?php

namespace Drupal\location\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;

/**
 * Simple page controller for drupal.
 */
class Page extends ControllerBase {

  /**
   * Lists the examples provided by form_example.
   */
  public function description() {
    // These libraries are required to facilitate the ajax modal form demo.
    $content['#attached']['library'][] = 'core/drupal.ajax';
    $content['#attached']['library'][] = 'core/drupal.dialog';
    $content['#attached']['library'][] = 'core/drupal.dialog.ajax';
   
    $header = array(
      // We make it sortable by name.
      array('data' => $this->t('Title')),
      array('data' => $this->t('Add')),
      array('data' => $this->t('Action')),
    );

    //Create a list of links to the form examples.
    $content['links'] = [
      '#theme' => 'item_list',
      '#items' => [
        $this->t('Add/Create/Update Location Master'),
        
      ],
    ];

    $rows[] = array(array('data' => $this->t('Country Master'), 'align' => 'left'),
                    array('data' => Link::createFromRoute($this->t('Add Country'), 'location.country_form'), 'align' => 'left'),
                    array('data' => Link::createFromRoute($this->t('View Country'), 'location.view_country'), 'align' => 'left'),);
    $rows[] = array(array('data' => $this->t('State Master'), 'align' => 'left'),
                    array('data' => Link::createFromRoute($this->t('Add State'), 'location.state_form'), 'align' => 'left'),
                    array('data' => Link::createFromRoute($this->t('View State'), 'location.view_state'), 'align' => 'left'),);
    $rows[] = array(array('data' => $this->t('City Master'), 'align' => 'left'),
                    array('data' => Link::createFromRoute($this->t('Add City'), 'location.city_form'), 'align' => 'left'),
                    array('data' => Link::createFromRoute($this->t('View City'), 'location.view_city'), 'align' => 'left'),);

     // Generate the table.
      $content['config_table'] = array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      );

    // The message container is used by the modal form example. It is an empty
    // tag that will be replaced by content.
    
    return $content;
  }

}
