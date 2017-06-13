<?php
namespace Drupal\cogecotv_base\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;

class PublishVideo {
    public static function formAlter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
      $form['field_tv_show']['widget']['#ajax'] = [
        'callback' => '\Drupal\cogecotv_base\Form\PublishVideo::ajax',
      ];
    }


    public static function ajax($form, FormStateInterface $form_state) {
      $response = new AjaxResponse();
      $response->addCommand(new ReplaceCommand(
        '#edit-field-community',
        'coucou'));
      return $response;

    }
}