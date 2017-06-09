<?php
namespace Drupal\cogecotv_base;

use Drupal\taxonomy\Entity\Term;

class Community extends Term {
  public static function communityTree() {
    $taxonomy_term_tree = new TaxonomyTermTree(\Drupal::service('entity_type.manager'));
    return $taxonomy_term_tree->load('communities');
  }

 public static function load($machine_name) {
    $result = \Drupal::service('entity.query')
      ->get('taxonomy_term')
      ->condition('field_machine_name', $machine_name)
      ->execute();
    if (!empty($result)) {
      $term = Term::load(current($result));
      return $term;
   }
  }
  
  public function getProvince() {
    $storage = \Drupal::service('entity_type.manager')->getStorage('taxonomy_term');
    $current_community_id = $this->id();
    $parents = $storage->loadParents($current_community_id);
    $province = current($parents);
  }
}