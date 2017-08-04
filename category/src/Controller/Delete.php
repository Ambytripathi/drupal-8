<?php

namespace Drupal\category\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Simple page controller for drupal.
 */
class Delete extends ControllerBase {

  /**
   * Lists the examples provided by form_example.
   */
  public function subcategory() {

    $cid = \Drupal::routeMatch()->getRawParameter('id');

    $squery = db_select('custom_offers_master','category');
    $squery->fields('category');
    $squery->condition('id',$cid,'=');
    $sresult = $squery->execute();
    $sdata = $sresult->fetchObject();

    $redirect = $sdata->parent_id;

    $num_deleted =  db_delete('custom_offers_master')
                    ->condition('id', $cid)
                    ->execute();

    if($num_deleted>0){
      drupal_set_message("Category deleted successfully!");
    }else{
      drupal_set_message(t("Detail not deleted."), 'warning');
    }

    if($redirect=='0'){
      return $this->redirect('category.view_category');
    }else{
      return $this->redirect('category.view_sub_category');
    }
    
  }

}
