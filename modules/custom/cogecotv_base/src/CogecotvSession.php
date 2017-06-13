<?php
/**
 * Created by PhpStorm.
 * User: c_yrocq
 * Date: 2017-05-15
 * Time: 15:23
 */

namespace Drupal\cogecotv_base;

use Drupal\user\PrivateTempStoreFactory;
use Drupal\Core\Session\SessionManager;

/**
 * Main CogecotvSession class
 */

class CogecotvSession  {
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

  public function getCurrentCommunity() {
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

  public function getCurrentProvince(){
    $community = $this->getCurrentCommunity();
    $province = NULL;

    if(!empty($community)) {
      $storage = \Drupal::service('entity_type.manager')->getStorage('taxonomy_term');
      $current_community_id = $community->id();
      $parents = $storage->loadParents($current_community_id);
      $province = current($parents);
    }

    return $province;
  }
}