<?php
namespace Drupal\cogecotv_base\Plugin\Block;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\cogecotv_base\Cogecotv;
use Drupal\Core\Block\BlockBase;

abstract class CogecoTvBlock extends BlockBase implements ContainerFactoryPluginInterface {
  protected $cogecotv;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, Cogecotv $cogecotv) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->cogecotv = $cogecotv;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $cogecotv = \Drupal::service('cogecotv_base.cogecotv');
    return new static(
      $configuration,
        $plugin_id,
        $plugin_definition,
      $cogecotv
    );
  }
}