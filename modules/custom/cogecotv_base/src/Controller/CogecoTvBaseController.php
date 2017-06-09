<?php
namespace Drupal\cogecotv_base\Controller;

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

  protected $cogecotv;

  /**
   * {@inheritdoc}
   */

  public function __construct($cogecotv) {
    $this->cogecotv = $cogecotv;
  }

  public static function create(ContainerInterface $container)
  {
    $cogecotv = $container->get('cogecotv_base.cogecotv');
    return new static($cogecotv);
  }


  /**
   * {@inheritdoc}
   */
  public function home_page() {
    $community = $this->cogecotv->getCommunity();

    if (!empty($community)) {
     $url = Url::fromRoute('cogecotv_base.community_home_page', ['community' => $community->field_machine_name->getString()])->toString();
    }
    else {
      $url = Url::fromRoute('cogecotv_base.community_selection_page')->toString();
    }

    return new RedirectResponse($url);
  }

    /**
   * {@inheritdoc}
   */
  public function community_subpage($community, $page) {
    $community = $this->cogecotv->getCommunity();
    $page_name = $this->cogecotv->matchQuicklink('community_navigation', $community, '/' . $page);
    $method_name = 'community_subpage_' . $page_name;

    if (method_exists($this, $method_name)) {
      return $this->$method_name();
    }
    else {
      throw new NotFoundHttpException();
    }
  }

  public function community_subpage_shows() {
    $community = $this->cogecotv->getCommunity();

    $view = Views::getView('shows');
    $build['view'] = $view->buildRenderable('embed_1',array($community->field_machine_name->getString()));

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
    $community = $this->cogecotv->getCommunity();
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
}
