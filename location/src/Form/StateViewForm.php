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
class StateViewForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $query = db_select('custom_country','country');
    $query->fields('country', ['id', 'country_name']);
    $result = $query->execute();
    $data = $result->fetchAll();
    foreach ($data as $value) {
      $arrayName[$value->id]=t($value->country_name);
    }

    $form['links'] = [
      '#theme' => 'item_list',
      '#items' => [
        Link::createFromRoute($this->t('&laquo; Back'), 'location.description'),
      ],
    ];

    $form['country_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Country Name'),
      '#options' => $arrayName,
      '#empty_option' => $this->t('-Select Country Name-'),
      '#ajax' => array(
                        'callback' => '::_ajaxfunction',
                        'wrapper' => 'getstate'
                      ),
    ];


        

    $form['state_id'] = [
      '#type' => 'markup',
      '#markup' => t('Please select country name first.'),
      '#prefix' => '<div id="getstate">',
      '#suffix' => '</div>',
    ];


  
    return $form;
   }

   /**
   * {@inheritdoc}
   */
   public function _ajaxfunction(array &$form, FormStateInterface $form_state) {

      $header = array(
        array('data' => $this->t('ID')),
        array('data' => $this->t('State Code')),
        array('data' => $this->t('State Name')),
        array('data' => $this->t('Description')),
        array('data' => $this->t('Action')),
      );
      
      $cid = $form_state->getValue('country_id');

      $squery = db_select('custom_state','state');
      $squery->fields('state', ['id', 'state_name', 'state_alias_name', 'description']);
      $squery->condition('country_id',$cid,'=');
      $sresult = $squery->execute();
      $sdata = $sresult->fetchAll();

      $post = array();
      foreach ($sdata as $value) {

         $post[] =
              array(
                $value->id,
                $value->state_alias_name,
                $value->state_name,
                $value->description,
                Link::createFromRoute($this->t('Edit'), 'location.edit_state', array('id'=>$value->id)),
              );

         $i++;
      }
    

      $form['state_id'] = array(
        '#theme' => 'table',
        '#header' => $header,
        '#rows' => $post,
      );

      
      $ajax_response = new AjaxResponse();
      $ajax_response->addCommand(new HtmlCommand('#getstate', $form['state_id']));
      return $ajax_response;
   }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'location_state_view_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
  }

}
