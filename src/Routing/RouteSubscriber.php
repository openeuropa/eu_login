<?php

declare(strict_types = 1);

namespace Drupal\oe_authentication\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection): void {
    // Only the user 1 has access to user creation on Drupal.
    $admin_routes = [
      'user.admin_create',
    ];
    foreach ($admin_routes as $admin_route) {
      if (($route = $collection->get($admin_route)) === NULL) {
        continue;
      }
      $route->setRequirement('_superuser_access_check', 'TRUE');
    }

    // Restrict Drupal login to Drupal only users.
    $internal_routes = [
      'user.pass',
      'user.pass.http',
      'user.login.http',
      'user.logout.http',
    ];
    foreach ($internal_routes as $internal_route) {
      if (($route = $collection->get($internal_route)) === NULL) {
        continue;
      }
      $route->setRequirement('_external_user_access_check', 'TRUE');

    }

    // Replace the core register route controller.
    $route = $collection->get('user.register');
    if ($route instanceof Route) {
      $defaults = $route->getDefaults();
      unset($defaults['_form']);
      $defaults['_controller'] = '\Drupal\oe_authentication\Controller\RegisterController::register';
      $route->setDefaults($defaults);
    }

    // Replace the cas callback route controller.
    if ($route = $collection->get('cas.proxyCallback')) {
      $route->setDefaults([
        '_controller' => '\Drupal\oe_authentication\Controller\ProxyCallbackController::callback',
      ]);
    }
  }

}
