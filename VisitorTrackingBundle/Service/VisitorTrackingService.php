<?php

namespace Daveudaimon\VisitorTrackingBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Daveudaimon\VisitorTrackingBundle\Entity\Visitor;
use Daveudaimon\VisitorTrackingBundle\Entity\VisitorHit;

class VisitorTrackingService
{
  protected $doctrine;

  public function __construct($doctrine)
  {
    $this->doctrine = $doctrine;
  }

  public function getEntityManager()
  {
    return $this->doctrine->getManager();
  }

  public function trackVisitor(Request $request, $name, array $options = array())
  {
    $session = $request->getSession();
    $em = $this->getEntityManager();

    // get visitor
    if (!$session->has('visitor_id'))
    {
      $visitor = new Visitor();
      $visitor->setIp($request->getClientIp());
      $visitor->setReferer($request->headers->get('referer') ? $request->headers->get('referer') : '');

      // set context
      if (isset($options['context']))
      {
        $visitor->setContext($options['context']);
      }

      $em->persist($visitor);
      $em->flush();

      $session->set('visitor_id', $visitor->getId());
    }
    else
    {
      $visitor = $em->getRepository('DaveudaimonVisitorTrackingBundle:Visitor')->find($session->get('visitor_id'));
    }

    // save hit
    if ($visitor)
    {
      $hit = new VisitorHit();
      $hit->setVisitor($visitor);
      $hit->setName($name);

      if (isset($options['target']))
      {
        $hit->setTarget($options['target']);
      }

      $em->persist($hit);
      $em->flush();
    }
  }

  public function getVisitorCount($name, $conditions=array())
  {
    $em = $this->getEntityManager();

    $visitorIds = null;
    if (!empty($conditions))
    {
      $visitorIds = $em->getRepository('DaveudaimonVisitorTrackingBundle:Visitor')->getVisitorIds($conditions);
    }

    return $em->getRepository('DaveudaimonVisitorTrackingBundle:VisitorHit')->getVisitorCount($name, $visitorIds);
  }
}
