<?php
namespace Drupal\cogecotv_base\Cache\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Drupal\cogecotv_base\Cogecotv;

/**
 * Defines the CurrentCommunityContext service, for "current community" caching.
 *
 * Cache context ID: 'cache_context.current_community'.
 */
class CurrentCommunityCacheContext implements CacheContextInterface {

    protected $cogecotv;

    public function __construct(Cogecotv $cogecotv) {
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
        return $this->cogecotv->getCommunity()->field_machine_name->getString();
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheableMetadata() {
        return new CacheableMetadata();
    }

}
