<?php
/**
 * @file
 * Contains \Drupal\cogecotv_base\EventSubscriber\EventSubscriber.
 */

namespace Drupal\cogecotv_base\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Drupal\Core\Url;

use \Drupal\cogecotv_base\Community;

/**
 * Event Subscriber EventSubscriber.
 */
class EventSubscriber implements EventSubscriberInterface {

  /**
   * Code that should be triggered on event specified
   */
  public function onRequest(GetResponseEvent $event) {
    $session =\Drupal::service('cogecotv_base.session');

    // The RESPONSE event occurs once a response was created for replying to a request.
    // For example you could override or add extra HTTP headers in here
    $request = $event->getRequest();
    $request_path = urldecode(trim($request->getPathInfo(), '/'));

    if (empty($session->getCurrentCommunity()) && '/' . $request_path != Url::fromRoute('cogecotv_base.community_selection_page')->toString()) {
      $url = Url::fromRoute('cogecotv_base.community_selection_page')->toString();
      $event->setResponse(new RedirectResponse($url));
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST ][] = ['onRequest'];
    return $events;
  }

}