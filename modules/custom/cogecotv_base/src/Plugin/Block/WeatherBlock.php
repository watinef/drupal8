<?php
/**
 * Created by PhpStorm.
 * User: karimrachadi
 * Date: 2017-05-24
 * Time: 09:55
 */

namespace Drupal\cogecotv_base\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'Weather' block.
 *
 * @Block(
 *   id = "cgtv_weather_block",
 *   admin_label = @Translation("Weather block"),
 * )
 */

class WeatherBlock extends BlockBase{
    function build() {
        return array(
            '#theme' => 'weather',
        );
    }
}