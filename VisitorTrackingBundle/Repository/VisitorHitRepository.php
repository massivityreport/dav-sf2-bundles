<?php
namespace Daveudaimon\VisitorTrackingBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VisitorHitRepository extends EntityRepository
{
  public function getVisitorCount($name, $visitorIds = null)
  {
    if (empty($name))
    {
      return 0;
    }
    if (null !== $visitorIds && empty($visitorIds))
    {
      return 0;
    }

    $qb = $this->getBaseQueryBuilder($name, $visitorIds)
        ->select('count(distinct h.visitor)');

    return $qb->getQuery()->getSingleScalarResult();
  }

  public function getVisitorList($name, $visitorIds = null)
  {
    if (empty($name))
    {
      return array();
    }
    if (null !== $visitorIds && empty($visitorIds))
    {
      return array();
    }

    $qb = $this->getBaseQueryBuilder($name, $visitorIds)
        ->leftJoin('h.visitor', 'v')
        ->orderBy('h.created', 'desc')
        ->groupBy('v.id');

    return $qb->getQuery()->getResult();
  }

  protected function getBaseQueryBuilder($name, $visitorIds = null)
  {
    $qb = $this->createQueryBuilder('h');

    if(is_array($name))
    {
      $qb
        ->where('h.name IN (:name)')
        ->setParameter('name', $name);
    }
    else
    {
      $qb
        ->where('h.name = :name')
        ->setParameter('name', $name);
    }

    if (null !== $visitorIds && !empty($visitorIds))
    {
      $qb->andwhere('h.visitor in (:visitorIds)')
        ->setParameter('visitorIds', $visitorIds);
    }

    return $qb;
  }
}
