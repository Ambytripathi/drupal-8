<?php

namespace Drupal\category\Controller;

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
        $this->t('Add/Create/Update Category Master'),
        
      ],
    ];
    // $rows[] = array(array('data' => $this->t('Category Master'), 'align' => 'left'),
    //                 array('data' => $this->t('Category Master'), 'align' => 'left'),
    //                 array('data' => $this->t('Category Master'), 'align' => 'left'),);

    $rows[] = array(array('data' => $this->t('Category Master'), 'align' => 'left'),
                    array('data' => Link::createFromRoute($this->t('Add Category'), 'category.category_form'), 'align' => 'left'),
                    array('data' => Link::createFromRoute($this->t('View Category'), 'category.view_category'), 'align' => 'left'),);

    $rows[] = array(array('data' => $this->t('Sub Category Master'), 'align' => 'left'),
                    array('data' => Link::createFromRoute($this->t('Add Sub Category'), 'category.sub_category_form'), 'align' => 'left'),
                    array('data' => Link::createFromRoute($this->t('View Sub Category'), 'category.view_sub_category'), 'align' => 'left'),);
    

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
