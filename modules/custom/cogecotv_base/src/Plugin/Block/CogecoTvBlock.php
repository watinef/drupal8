<?php
namespace Drupal\cogecotv_base\Plugin\Block;
use Drupal\cogecotv_base\CogecoTvNavigation;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\cogecotv_base\CogecotvSession;
use Drupal\Core\Block\BlockBase;

abstract class CogecoTvBlock extends BlockBase implements ContainerFactoryPluginInterface {
  protected $session;
  protected $navigation;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, CogecotvSession $session, CogecoTvNavigation $navigation) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->session = $session;
    $this->navigation = $navigation;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $session = \Drupal::service('cogecotv_base.session');
    $navigation = \Drupal::service('cogecotv_base.navigation');
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $session,
      $navigation
    );
  }
}