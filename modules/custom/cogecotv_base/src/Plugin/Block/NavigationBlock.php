<?php
/**
 * Created by PhpStorm.
 * User: karimrachadi
 * Date: 2017-05-16
 * Time: 11:39
 */


namespace Drupal\cogecotv_base\Plugin\Block;

use  Drupal\cogecotv_base\Plugin\Block\CogecoTvBlock;

/**
* Provides a 'Top Header' block.
*
* @Block(
*   id = "cgtv_navigation_block",
*   admin_label = @Translation("Navigation block"),
* )
*/

class NavigationBlock extends CogecoTvBlock {

  function build() {
    $result = [];
    $community = $this->cogecotv->getCommunity();
    if ($community) {
      $quicklinks = $this->cogecotv->quickLinks('community_navigation', $this->cogecotv->getCommunity());

      foreach ($quicklinks as  &$value) {
        $value['url'] = '/' . $community->field_machine_name->getString() . $value['url'];
      }

      return [
        '#theme' => 'navigation',
        '#quicklinks' => $quicklinks,
      ];
    }
  }
}