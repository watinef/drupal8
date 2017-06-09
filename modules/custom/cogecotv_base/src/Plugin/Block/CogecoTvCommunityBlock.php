<?php
namespace Drupal\cogecotv_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use  Drupal\cogecotv_base\Cogecotv;
use  Drupal\cogecotv_base\Plugin\Block\CogecoTvBlock;

/**
* Provides a 'Community' block.
*
* @Block(
*   id = "cogecotv_base_community_block",
*   admin_label = @Translation("Community block"),
* )
*/
class CogecoTvCommunityBlock extends CogecoTvBlock {
  function build() {
    $community = $this->cogecotv->getCommunity();
    if ($community) {
      return array(
        '#markup' => '<h1>' . Link::createFromRoute($community->getName(), 'cogecotv_base.community_selection_page')->toString() . '</h1><h2>' . $community->field_station->value . '</h2>',
        '#cache' => array(
            'contexts' => array('current_community'),
        ),
      );
    }
  }
}

// ontario : windsor