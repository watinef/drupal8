<?php
/**
 * Created by PhpStorm.
 * User: c_yrocq
 * Date: 2017-05-15
 * Time: 15:23
 */

namespace Drupal\cogecotv_base;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Drupal\user\PrivateTempStoreFactory;
use Drupal\Core\Session\SessionManager;

/**
 * Main Cogecotv class
 */

class Cogecotv  {
  protected $session_manager;
  protected $tempstore;

  public function __construct(SessionManager $session_manager, PrivateTempStoreFactory $tempstore) {
    $this->session_manager = $session_manager;
    $this->tempstore = $tempstore;
  }

  public function setCommunity($community) {
  $this->startAnonymousSession();
    $this->tempstore->get('cogecotv_base')->set('current_community', $community);
  }

  public function getCommunity() {
    $community =  Community::load(\Drupal::routeMatch()->getParameter('community'));
    if (empty($community)) {
      $community = $this->getCommunityBySession();
    }
    return $community;
  }

  public function getCommunityBySession() {
    $community = NULL;
   // Bypass drush bug
   if (php_sapi_name() != 'cli') {
     $this->startAnonymousSession();
     $community = $this->tempstore->get('cogecotv_base')->get('current_community');
   }


    return $community;
  }

    public function startAnonymousSession() {
    if (\Drupal::currentUser()->isAnonymous() && !isset($_SESSION['session_started'])) {
      $_SESSION['session_started'] = true;
      $this->session_manager->start();
    }
  }

  public function getProvince(){
    $community = $this->getCommunity();
    $province = NULL;

    if(!empty($community)) {
      $storage = \Drupal::service('entity_type.manager')->getStorage('taxonomy_term');
      $current_community_id = $community->id();
      $parents = $storage->loadParents($current_community_id);
      $province = current($parents);
    }

    return $province;
  }

  public function quickLinks($type, $community) {
    $config = \Drupal::config('cogecotv_base.quicklinks');
    $quicklinks = [];

    if (!empty($config)) {
      foreach ($config->get($type) as $config_key => $config_value) {

        $item =  $this->normalizeCommunityData($config_value, $community);
        $quicklinks[$config_key] = array(
          'title' => t($item['title']),
          'url' => t($item['url']),
        );
      }
    }

    return $quicklinks;
  }

  public function matchQuicklink($type, $community, $path) {
    $page = NULL;
    $config = \Drupal::config('cogecotv_base.quicklinks');

    if (!empty($config)) {
      foreach ($config->get($type) as $config_key => $config_value) {
        $item =  $this->normalizeCommunityData($config_value, $community);
        if ($item['url'] == $path) {
            $page = $config_key;
            break;
          }
      }
    }

    return $page;

  }

  public function normalizeCommunityData($data, $community, $language = NULL) {
    $province = $this->getProvince()->field_machine_name->getString();

    return array_merge($data['all'], !empty($data[$province])?$data[$province]:array());
  }
}