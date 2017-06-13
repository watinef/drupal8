<?php
namespace Drupal\cogecotv_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\cogecotv_base\Community;
use Drupal\cogecotv_base\CogecotvSession;
use Drupal\cogecotv_base\Plugin\Block\CogecoTvBlock;

/**
 * Provides a 'Community' block.
 *
 * @Block(
 *   id = "cogecotv_base_socialmedia_block",
 *   admin_label = @Translation("Social Media block"),
 * )
*/
class CogecoTvSocialMediaBlock extends CogecoTvBlock {

    function build() {
        $currentCommunity = $this->session->getCurrentCommunity();

        $build = array(
            '#theme' => 'social_media',
            '#social_media' => $this->get_social_media_field_values($currentCommunity),
            '#attached' => array(
                'library' => array(
                    'cogecotv_base/cogecotv.socialmedia',
                ),
            ),
            '#cache' => array(
                'contexts' => array('current_community'),
            ),
        );

        return $build;
    }

    function get_social_media_field_values($community) {
        $social_media_field_values = array();

        if(!is_null($community)) {
            $social_media_field_values['facebook'] = $community->field_social_facebook->uri;
            $social_media_field_values['twitter'] = $community->field_social_twitter->uri;
            $social_media_field_values['youtube'] = $community->field_social_youtube->uri;
        }

        return $social_media_field_values;
    }
}