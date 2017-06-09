<?php
namespace Drupal\cogecotv_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
* Provides a 'Header Links' block.
*
* @Block(
*   id = "cgtv_headerlinks_block",
*   admin_label = @Translation("Header Links block"),
* )
*/
class HeaderLinksBlock extends BlockBase {
  function build() {
    return array(
        '#theme' => 'header_links',
        '#cache' => array(
            'contexts' => array('current_community'),
        ),
    );
  }
}
