<?php
use Drupal\taxonomy\Entity\Term;
use Drupal\node\Entity\Node;

/**
 * Implements hook_drush_command().
 */
function cogecotv_fixtures_drush_command() {

  $commands['create-communities'] = [
    'description' => 'Empties Communities taxonomy and create test terms.',
    'aliases' => ['cgtv-cc'],
    'arguments' => [
    ],
    'options' => [
    ],
    'examples' => [
      'drush cgtv-cc' => 'Create communities.',
    ],
  ];

  $commands['fix-communities'] = [
    'description' => 'Fix communities language and machine name.',
    'aliases' => ['cgtv-fc'],
    'arguments' => [
    ],
    'options' => [
    ],
    'examples' => [
      'drush cgtv-cf' => 'Fix communities.',
    ],
  ];

    $commands['create-basic-nodes'] = [
        'description' => 'Create basic nodes for CogecoTV.',
        'aliases' => ['cgtv-bn'],
        'arguments' => [
        ],
        'options' => [
        ],
        'examples' => [
            'drush cgtv-bn' => 'Create Basic Nodes.',
        ],
    ];


  $commands['create-users'] = [
      'description' => 'Create a user for each role in CogecoTV.',
      'aliases' => ['cgtv-cu'],
      'arguments' => [
      ],
      'options' => [
      ],
      'examples' => [
          'drush cgtv-cu' => 'Create a user for each role.',
      ],
  ];



  return $commands;
}

/**
 * Drush command logic.
 * drush_[MODULE_NAME]_[COMMAND_NAME]().
 */
function drush_cogecotv_fixtures_create_communities() {
  // Create provinces
  $communities = array(
    array(
      'name' => 'Ontario',
      'field_machine_name' => 'ontario',
      'field_language' => 'en',
    ),
    array(
      'name' => 'Quebec',
      'field_machine_name' => 'quebec',
      'field_language' => 'fr',
    ),
    array(
      'name' => 'Ontario English',
      'field_machine_name' => 'ontario-english',
      'field_language' => 'en',
      'parent_machine_name' => 'ontario',
      'field_social_facebook' => 'https://facebook.cogeco.com/tvcogecoOntarioEnglish',
      'field_social_twitter' => 'https://twitter.cogeco.com/tvcogecoOntarioEnglish',
      'field_social_youtube' => 'https://youtube.cogeco.com/tvcogecoOntarioEnglish',
    ),
    array(
      'name' => 'Quebec English',
      'field_machine_name' => 'quebec-english',
      'field_language' => 'en',
      'parent_machine_name' => 'quebec',
    ),
    array(
      'name' => 'Ontario French',
      'field_machine_name' => 'ontario-french',
      'field_language' => 'fr',
      'parent_machine_name' => 'ontario',
      'field_social_twitter' => 'https://twitter.cogeco.com/tvcogecoOntarioFrench',
    ),
    array(
      'name' => 'Quebec French',
      'field_machine_name' => 'quebec-french',
      'field_language' => 'fr',
      'parent_machine_name' => 'quebec',
      'field_social_facebook' => 'https://facebook.cogeco.com/tvcogecoQuebecFrench',
      'field_social_twitter' => 'https://twitter.cogeco.com/tvcogecoQuebecFrench',
    ),
  );

  $tids = \Drupal::entityQuery('taxonomy_term')
    ->condition('vid', 'communities')
    ->execute();

  drush_print(dt('Deleting communities'));

  $controller = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
  $entities = $controller->loadMultiple($tids);
  $controller->delete($entities);

  drush_print(dt('Creating test communities'));

  foreach ($communities as $community) {
    $term_array = [
      'name' => $community['name'],
      'vid' => 'communities',
      'field_machine_name' => $community['field_machine_name'],
      'field_language' => $community['field_language'],
      'field_social_facebook' => array('uri' => $community['field_social_facebook']),
      'field_social_twitter' => array('uri' => $community['field_social_twitter']),
      'field_social_youtube' => array('uri' => $community['field_social_youtube']),
    ];

    if (!empty($community['parent_machine_name'])) {
      $parent = $terms[$community['parent_machine_name']];
      $term_array['parent'][0] = $parent->id();
    }


    $term = Term::create($term_array);
    $term->save();
    $terms[$community['field_machine_name']] = $term;
  }
}

/**
 * Drush command logic.
 * drush_[MODULE_NAME]_[COMMAND_NAME]().
 */
function drush_cogecotv_fixtures_fix_communities() {
  $em = \Drupal::service('entity_type.manager');
  $terms = $em->getStorage('taxonomy_term')->loadTree('communities', 0, NULL, TRUE);

  foreach($terms as $term) {
    $term->field_machine_name = $term->id();
    if (($term->parents[0] == 5 || $term->id() == 5) && $term->id() != 15 ) {
      $term->field_language = 'en';
    }
    else {
      $term->field_language = 'fr';
    }

    $term->save();
  }

}

/**
 * Drush command logic.
 * drush_[MODULE_NAME]_[COMMAND_NAME]().
 */
function drush_cogecotv_fixtures_create_basic_nodes() {
  $path_manager = \Drupal::service('path.alias_manager');

  if($path_manager->getPathByAlias('/help') && $path_manager->getPathByAlias('/help')) {
    drush_print(dt("Node already exists"));
  }
  else {
    //Creating FAQ node
    $node = Node::create([
      'type'             => 'page',
      'title'            => 'Frequently Asked Questions',
      'body'             => 'insert your content here!',
      'langcode'         => 'en',
      'format'           => 'basic_html',
      'moderation_state' => 'published',
      'uid'              => 1,
    ]);
    $node->save();

    //Translating FAQ node
    $node_fr = $node->addTranslation('fr');
    $node_fr->title = 'Questions / Réponses';
    $node_fr->body->value = 'inserer votre texte ici!';
    $node_fr->body->format = 'basic_html';
    $node_fr->save();
    $alias_storage = \Drupal::service('path.alias_storage');
    $alias_storage->delete(array('alias' =>  '/aide'));
    $alias_storage->delete(array('alias' =>  '/help'));
    $alias_storage->save("/node/" . $node->id(), "/aide", 'fr');
    $alias_storage->save("/node/" . $node->id(), "/help", 'en');

  }
}



/**
 * Drush command logic.
 * drush_[MODULE_NAME]_[COMMAND_NAME]().
 */
function drush_cogecotv_fixtures_create_users() {

  $language = \Drupal::languageManager()->getCurrentLanguage()->getId();

  $usersInfo = array(
      array('userName' => 'SystemAdmin', 'role' => 'administrator'),
      array('userName' => 'Webmaster','role' => 'webmaster'),
      array('userName' => 'StationManager','role' => 'station_manager'),
      array('userName' => 'LocalProducer', 'role' => 'local_producer'),
      );

  foreach($usersInfo as $userInfo) {
    $userName = $userInfo['userName'];

    $user = \Drupal\user\Entity\User::create();
    //Mandatory settings
    $user->setPassword('password');
    $user->enforceIsNew();
    $user->setEmail($userName . '@cogecotv.com');
    $user->setUsername($userName);//This username must be unique and accept only a-Z,0-9, - _ @ .

    //Optional settings
    $user->set('init', 'email');
    $user->set('langcode', $language);
    $user->set('preferred_langcode', $language);
    $user->set('preferred_admin_langcode', $language);
    $user->addRole($userInfo['role']);
    //$user->set("setting_name", 'setting_value');
    $user->activate();

    //Save user
    $res = $user->save();
  }


}
