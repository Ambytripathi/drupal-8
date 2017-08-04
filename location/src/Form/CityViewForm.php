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
class CityViewForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $query = db_select('custom_state','state');
    $query->fields('state', ['id', 'state_name']);
    $result = $query->execute();
    $data = $result->fetchAll();
  
    foreach ($data as $value) {
      $arrayName[$value->id]=t($value->state_name);
    }

    $form['links'] = [
      '#theme' => 'item_list',
      '#items' => [
        Link::createFromRoute($this->t('&laquo; Back'), 'location.description'),
      ],
    ];

    if(!empty($arrayName)){
      $form['state_id'] = [
        '#type' => 'select',
        '#title' => $this->t('State Name'),
        '#options' => @$arrayName,
        '#empty_option' => $this->t('-Select State Name-'),
        '#ajax' => array(
                          'callback' => '::_ajaxfunction',
                          'wrapper' => 'getstate'
                        ),
      ];
      $form['city_id'] = [
      '#type' => 'markup',
      '#markup' => t('Please select country name first.'),
      '#prefix' => '<div id="getstate">',
      '#suffix' => '</div>',
    ];
    }else{
      $form['state_id'] = [
        '#type' => 'markup',
        '#markup' => t('State name not avalable.'),
      ];
    }


        

    


  
    return $form;
   }

   /**
   * {@inheritdoc}
   */
   public function _ajaxfunction(array &$form, FormStateInterface $form_state) {

      $header = array(
        array('data' => $this->t('ID')),
        array('data' => $this->t('City Code')),
        array('data' => $this->t('City Name')),
        array('data' => $this->t('Description')),
        array('data' => $this->t('Action')),
      );

      $cid = $form_state->getValue('state_id');

      $squery = db_select('custom_city','city');
      $squery->fields('city', ['id', 'city_name', 'city_alias_name', 'description']);
      $squery->condition('state_id',$cid,'=');
      $sresult = $squery->execute();
      $sdata = $sresult->fetchAll();
      $total = count($sdata);
      $post = array();

      foreach ($sdata as $value) {

         $post[] =
              array(
                $value->id,
                $value->city_alias_name,
                $value->city_name,
                $value->description,
                Link::createFromRoute($this->t('Edit'), 'location.edit_city', array('id'=>$value->id)),
              );


      }


      
       $form['city_id'] = array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $post,
      );


      $ajax_response = new AjaxResponse();
      $ajax_response->addCommand(new HtmlCommand('#getstate',  $form['city_id']));
      return $ajax_response;

   }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'location_city_view_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
  }

}
