<?php
namespace Drupal\cogecotv_base\Controller;

use Drupal\cogecotv_base\CogecoTvNavigation;
use Drupal\cogecotv_base\Community;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\views\Views;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
* Cogeco TV base controller
*/
class CogecoTvBaseController extends ControllerBase {

  protected $session;
  protected $navigation;

  /**
   * {@inheritdoc}
   */

  public function __construct($cogecotv, $navigation) {
    $this->session = $cogecotv;
    $this->navigation = $navigation;
  }

  public static function create(ContainerInterface $container)
  {
    $cogecotv = $container->get('cogecotv_base.session');
    $navigation = $container->get('cogecotv_base.navigation');
    return new static($cogecotv, $navigation);
  }


  /**
   * {@inheritdoc}
   */
  public function home_page() {
    $community = $this->session->getCurrentCommunity();

    if (!empty($community)) {
     $url = Url::fromRoute('cogecotv_base.community_home_page', ['community' => $community->field_machine_name->getString()])->toString();
    }
    else {
      $url = Url::fromRoute('cogecotv_base.community_selection_page')->toString();
    }

    return new RedirectResponse($url);
  }

    /**
     * @param $community
     * @param $page
     * @return mixed
     */
  public function community_subpage($community, $page) {
    $page_name = $this->navigation->matchQuicklink('community_navigation', $community, '/' . $page);
    $method_name = 'community_subpage_' . $page_name;

    if (method_exists($this, $method_name)) {
      return $this->$method_name();
    }
    else {
      throw new NotFoundHttpException();
    }
  }

  public function community_subpage_shows() {
    $community = $this->session->getCurrentCommunity();
    $view = Views::getView('shows');
    $build['view'] = $view->buildRenderable('embed_1', array($community->field_machine_name->getString()));

    return $build;
  }

  public function community_subpage_videos() {
    $community = $this->session->getCurrentCommunity();

    $view = Views::getView('videos');
    $build['view'] = $view->buildRenderable('embed_1', array($community->field_machine_name->getString()));

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function community_selection_page() {
    $build = [
      '#theme' => 'community_selection',
      '#communities' => Community::communityTree(),
    ];

    return $build;
  }

  public function community_home_page() {
    $community = $this->session->getCurrentCommunity();
    $build = array(
      '#theme' => 'community_home_page',
      '#community' => $community,
    );

    return $build;
  }

  public function error_404() {
    $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $vars['language'] = $language;

    $build = array(
      '#theme' => 'error_404',
      '#cache' => array(
        'contexts' => array('url'),
      ),
    );

    return $build;
  }

    public static function static_page(){
        $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $page_name =  \Drupal::routeMatch()->getParameter('page_name');


        $build = array(
            '#theme' => 'static_page',
            '#page_name' => $page_name,
        );

        return $build;

    }

  /**
   * Get Title
   * @param $community
   * @param $page
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|void
   */
  public function communitySubpageTitle($community, $page) {
    $get_title = $this->navigation->getTitlePage($community, $page);
    $title = !empty($get_title) ? $get_title : '';
    return t($title);
  }
}
