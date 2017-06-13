<?php
namespace Drupal\cogecotv_base;

class CogecoTvNavigation {
  protected $session;

  public function __construct($session) {
    $this->session = $session;
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

        $item =  $this->normalizeCommunityData($config_value, Community::load($community));
        if ($item['url'] == $path) {
          $page = $config_key;
          break;
        }
      }
    }

    return $page;
  }

  public function normalizeCommunityData($data, $community, $language = NULL) {

   $province = Community::getProvince($community)->field_machine_name->getString();
    return array_merge($data['all'], !empty($data[$province])?$data[$province]:array());
  }

  /**
   * Get Title from community
   * @param $community
   * @param $page
   * @return string|void
   */
  public function getTitlePage($community, $page) {
    return $this->extractField($community, $page, '_title');
  }

  /**
   * Get Subtitle from community
   * @param $community
   * @param $page
   * @return string|void
   */
  public function getSubTitlePage($community, $page) {
    return $this->extractField($community, $page, '_subtitle');
  }

  /**
   * @param $community
   * @param $page
   * @param $fieldToModify
   * @return string|void
   */
  private function extractField($community, $page, $fieldToModify) {
    $page_name = $this->matchQuicklink('community_navigation', $community, '/' . $page);
    $has_title = ['shows', 'videos'];
    if (!in_array($page_name, $has_title)) {
      return;
    }
    $community = $this->session->getCurrentCommunity();
    $field = 'field_' . $page_name . $fieldToModify;
    $page_value = $community->get($field)->value;
    $title = (!empty($page_value)) ? $community->$field->getString() : '';

    return $title;
  }

}