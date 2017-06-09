<?php
namespace Drupal\cogecotv_base;

use Drupal\Core\Entity\EntityTypeManager;

/**
 * Loads taxonomy terms in a tree
 */
class TaxonomyTermTree {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * TaxonomyTermTree constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   */
  public function __construct(EntityTypeManager $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Loads the tree of a vocabulary.
   *
   * @param string $vocabulary
   *   Machine name
   *
   * @return array
   */
  public function load($vocabulary) {
    $tree = [];
    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($vocabulary, 0, NULL, TRUE);

    foreach ($terms as $term)
    {
      $parent = $term->parents[0];

      if ($parent == 0) {
       $tree [$term->id()]['name'] = $term->name->getString();
      }
      else {
        $tree[$parent]['children'][$term->id()]['name'] = $term->name->getString();
        $tree[$parent]['children'][$term->id()]['machine_name'] = $term->field_machine_name->getString();
      }
    }

    return $tree;
  }

}