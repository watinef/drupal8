<?php
namespace Drupal\cogecotv_base\Cache\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Drupal\cogecotv_base\CogecotvSession;

/**
 * Defines the CurrentCommunityContext service, for "current community" caching.
 *
 * Cache context ID: 'cache_context.current_community'.
 */
class CurrentCommunityCacheContext implements CacheContextInterface {

    protected $cogecotv;

    public function __construct(CogecotvSession $cogecotv) {
        $this->cogecotv = $cogecotv;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function getLabel() {
        return t('Cogeco Tv Community');
    }

    /**
     * {@inheritdoc}
     */
    public function getContext() {
        if ($community = $this->cogecotv->getCurrentCommunity()) {
          $context =  $community->field_machine_name->getString();
        }
        else {
          $context = 'no-community';
        }

        return $context;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheableMetadata() {
        return new CacheableMetadata();
    }

}
