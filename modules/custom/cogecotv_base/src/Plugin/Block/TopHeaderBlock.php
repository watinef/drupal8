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
*   id = "cgtv_topheader_block",
*   admin_label = @Translation("Top Header block"),
* )
*/

class TopHeaderBlock extends CogecoTvBlock {

  function build() {
    if ($community = $this->session->getCurrentCommunity()) {
      $quicklinks = $this->navigation->quickLinks('top_header', $community);

      return [
        '#theme' => 'top_header',
        '#quicklinks' => $quicklinks,
        '#cache' => array(
          'contexts' => array('current_community'),
        ),
      ];
    }
  }
}