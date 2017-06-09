<?php

namespace Drupal\cogecotv_base\Plugin\LanguageNegotiation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\cogecotv_base\Community;
use Drupal\language\Plugin\LanguageNegotiation\LanguageNegotiationUrl;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Url;



/**
 * Class for identifying language via Cogeco TV community or prefix
 *
 * @LanguageNegotiation(
 *   id = \Drupal\cogecotv_base\Plugin\LanguageNegotiation\LanguageNegotiationCommunity::METHOD_ID,
 *   types = {\Drupal\Core\Language\LanguageInterface::TYPE_INTERFACE,
 *   \Drupal\Core\Language\LanguageInterface::TYPE_CONTENT,
 *   \Drupal\Core\Language\LanguageInterface::TYPE_URL},
 *   weight = -7,
 *   name = @Translation("Cogeco TV community"),
 *   description = @Translation("Language from the current Cogeco TV community page or from the URL if not on community page.")
 * )
 */
class LanguageNegotiationCommunity extends LanguageNegotiationUrl {

  /**
   * The language negotiation method id.
   */
  const METHOD_ID = 'community';


  /**
   * {@inheritdoc}
   */
  public function getLangcode(Request $request = NULL) {
    $langcode = NULL;
    $cogecotv =\Drupal::service('cogecotv_base.cogecotv');
    $community = NULL;
    $community_selection_url = Url::fromRoute('cogecotv_base.community_selection_page')->toString();

    if ($request && $this->languageManager) {
      // It is too soon too use the routing system so, we have to parse url
      $request_path = urldecode(trim($request->getPathInfo(), '/'));
      $path_args = explode('/', $request_path);

      // Use community language if we are on path /community/*

      $community_from_path = Community::load($path_args[0]);

      if (!empty($community_from_path)) {
        $community = $community_from_path;
        $cogecotv->setCommunity($community);
      }
      else {
        $community = $cogecotv->getCommunity();
      }
      if ($community) {
        $community_language = $community->field_language->getString();
        $languages = $this->languageManager->getLanguages();

        $negotiated_language = FALSE;
        foreach ($languages as $language) {
          if ($language->getId() == $community_language) {
            $negotiated_language = $language;
            break;
          }
        }

        if ($negotiated_language) {
          $langcode = $negotiated_language->getId();
        }
      }
    }
    return $langcode;
  }

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    $parts = explode('/', trim($path, '/'));
    $community = Community::load($parts[0]);

    if ($community) {
      $path = '/community/' . implode('/', $parts);
    }
    else {
        $prefix = array_shift($parts);

        // Search prefix within added languages.
        if ($prefix == 'fr') {
          // Rebuild $path with the language removed.
          $path = '/' . implode('/', $parts);
        }
      }

    return $path;
  }


    /**
   * {@inheritdoc}
   */
  public function processOutbound($path, &$options = [], Request $request = NULL, BubbleableMetadata $bubbleable_metadata = NULL) {
    $parts = explode('/', trim($path, '/'));
    // Add language prefix in links only if we are not on
    // a community page

    if ($parts[0] == 'community') {
      array_shift($parts);
      $path = '/' . implode('/', $parts);
    }
    else {
      $path = parent::processOutbound($path, $options, $request, $bubbleable_metadata);
    }

    return $path;
  }
}
