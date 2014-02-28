<?php

namespace Daveudaimon\UserTrackingBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Daveudaimon\UserTrackingBundle\Entity\UserHit;

class UserTrackingService
{
  protected $doctrine;
  protected $securityContext;

  public function __construct($doctrine, $securityContext)
  {
    $this->doctrine = $doctrine;
    $this->securityContext = $securityContext;
  }

  public function getEntityManager()
  {
    return $this->doctrine->getManager();
  }

  public function getSecurityToken()
  {
    return $this->securityContext->getToken();
  }

  public function trackUser($name, array $options = array())
  {
    // get user
    $user = $this->getSecurityToken()->getUsername();

    // save hit
    if ($user)
    {
      $em = $this->getEntityManager();

      $hit = new UserHit();
      $hit->setUser($user);
      $hit->setName($name);

      if (isset($options['target']))
      {
        $hit->setTarget($options['target']);
      }

      $em->persist($hit);
      $em->flush();
    }
  }
}
