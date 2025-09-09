<?php

namespace Drupal\url_alteration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginController extends ControllerBase
{
    /**
     * Returns a simple page with a login form.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function userLogin(): RedirectResponse
    {
        // Redirect if already logged in
        if (\Drupal::currentUser()->isAuthenticated()) {
            return new RedirectResponse('/');
        }

        // Extract referer
        $request = \Drupal::request();
        $referer = $request->headers->get('referer');
        $destination = '/';

        if (!empty($referer)) {
            // Strip domain from referer (normalize to internal path)
            $destination = parse_url($referer, PHP_URL_PATH) ?? '/';

            // Avoid open redirect attempts
            if (!Url::fromUserInput($destination)->isRouted()) {
                $destination = '/';
            }
        }

        // Build login URL with destination
        $login_url = Url::fromRoute('simplesamlphp_auth.saml_login', [], [
            'query' => ['destination' => $destination],
        ])->toString();

        return new RedirectResponse($login_url);
    }
}
