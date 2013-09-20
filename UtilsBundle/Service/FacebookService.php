<?php

namespace daveudaimon\UtilsBundle\Service;

use Symfony\Component\HttpFoundation\Request;

class FacebookService
{
  protected $app_ip;
  protected $app_secret;
  protected $session;

  public function __construct($app_id, $app_secret, $session)
  {
    $this->app_id = $app_id;
    $this->app_secret = $app_secret;
    $this->session = $session;
  }

  public function getLoginUrl($redirectUrl, array $permissions = array())
  {
    $crsf_token = md5(uniqid(rand(), TRUE));
    $this->session->set('facebook_crsf', $crsf_token);
    $this->session->set('facebook_redirect_url', $redirectUrl);

    $dialog_url = "https://www.facebook.com/dialog/oauth?client_id="
      . $this->app_id . "&redirect_uri=" . urlencode($redirectUrl) . "&state="
      . $crsf_token . "&scope=" . implode(',', $permissions);

    return $dialog_url;
  }

  public function processLoginRequest(Request $request)
  {
    if ($this->session->get('facebook_crsf') != $request->query->get('state'))
    {
      return array(
        'status' => 'error',
        'error'  => 'crsf_attack',
        );
    }

    if ($request->query->has('error'))
    {
      return array(
        'status' => 'error',
        'error'  => $request->query->get('error'),
      );
    }

    $code = $request->query->get('code');
    $redirectUrl = $this->session->get('facebook_redirect_url');

    $token_url = "https://graph.facebook.com/oauth/access_token?"
     . "client_id=" . $this->app_id . "&redirect_uri=" . urlencode($redirectUrl)
     . "&client_secret=" . $this->app_secret . "&code=" . $code;

    try
    {
      $response = file_get_contents($token_url);
      $params = null;
      parse_str($response, $params);
    }
    catch(\Exception $e)
    {
      return array(
        'status'  => 'error',
        'error'   => $e->getMessage(),
        );
    }

    return array(
      'status'        => 'success',
      'access_token'  => $params['access_token'],
      'expires'       => $params['expires'],
      );
  }

  public function exchangeAccessToken($access_token)
  {
    $token_url = "https://graph.facebook.com/oauth/access_token?"
      . "client_id=" . $this->app_id . "&client_secret=" . $this->app_secret
      . "&grant_type=fb_exchange_token&fb_exchange_token=".$access_token;

    try
    {
      $response = file_get_contents($token_url);
      $params = null;
      parse_str($response, $params);
    }
    catch(\Exception $e)
    {
      return array(
        'status'  => 'error',
        'error'   => $e->getMessage(),
        );
    }

    return array(
      'status'        => 'success',
      'access_token'  => $params['access_token'],
      'expires'       => $params['expires'],
      );
  }

  public function getUser($access_token, array $fields = array())
  {
    $graph_url = "https://graph.facebook.com/me?access_token="
     . $access_token;

     if (!empty($fields))
     {
      $graph_url .= "&fields=" . implode(',', $fields);
     }

    $user = json_decode(file_get_contents($graph_url));

    return $user;
  }

  public function getUserFeed($access_token)
  {
    $graph_url = "https://graph.facebook.com/me/feed?access_token="
     . $access_token;

    $feed = json_decode(file_get_contents($graph_url));

    return $feed->data;
  }

  public function getUserPosts($access_token)
  {
    $graph_url = "https://graph.facebook.com/me/posts?access_token="
     . $access_token;

    $posts = json_decode(file_get_contents($graph_url));

    return $posts->data;
  }

  public function getObject($access_token, $objectId)
  {
    $graph_url = "https://graph.facebook.com/".$objectId."?access_token="
     . $access_token;

    $object = json_decode(file_get_contents($graph_url));

    return $object;
  }
}
