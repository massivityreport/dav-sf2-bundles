<?php
namespace daveudaimon\VisitorTrackingBundle\Repository;

use Doctrine\ORM\EntityRepository;

class VisitorRepository extends EntityRepository
{
  public function getCount(array $conditions)
  {
    $qb = $this->getBaseQueryBuilder($conditions)
        ->select('count(v.id)');

    return $qb->getQuery()->getSingleScalarResult();;
  }

  public function getVisitorIds(array $conditions)
  {
    $qb = $this->getBaseQueryBuilder($conditions)
        ->select('v.id');

    return $qb->getQuery()->getArrayResult();
  }

  public function getContextList(array $conditions)
  {
    $qb = $this->getBaseQueryBuilder($conditions)
        ->select('distinct v.context');

    $contextList = array();
    foreach ($qb->getQuery()->getArrayResult() as $result)
    {
      if (!empty($result['context']))
      {
        $contextList[] = $result['context'];
      }
    }

    return $contextList;
  }

  protected function getBaseQueryBuilder(array $conditions)
  {
    $qb = $this->createQueryBuilder('v');

    if (isset($conditions['referer']) && !empty($conditions['referer']))
    {
      $qb->andWhere('v.referer like :referer');
      $qb->setParameter('referer', '%'.$conditions['referer'].'%');
    }

    if (isset($conditions['banned_ips']))
    {
      $bannedIps = explode("\r\n", trim($conditions['banned_ips']));
      if (!empty($bannedIps))
      {
        foreach($bannedIps as $i => $bannedIp)
        {
          if (preg_match("/(\d+.\d+.\d+\.\d+)\s*-\s*(\d+.\d+.\d+\.\d+)/", $bannedIp, $matches))
          {
            $qb->andWhere('NOT (inet_aton(v.ip) >= inet_aton(:banned_ip_range_from_'.$i.') AND inet_aton(v.ip) <= inet_aton(:banned_ip_range_to_'.$i.'))');
            $qb->setParameter('banned_ip_range_from_'.$i, $matches[1]);
            $qb->setParameter('banned_ip_range_to_'.$i, $matches[2]);
          }
          else
          {
            $qb->andWhere('v.ip != :banned_ip_'.$i);
            $qb->setParameter('banned_ip_'.$i, $bannedIp);
          }
        }
      }
    }

    if (isset($conditions['period_start']) && !empty($conditions['period_start']))
    {
      $qb->andWhere('v.created >= :period_start');
      $qb->setParameter('period_start', $conditions['period_start']);
    }

    if (isset($conditions['period_end']) && !empty($conditions['period_end']))
    {
      $qb->andWhere('v.created <= :period_end');
      $qb->setParameter('period_end', $conditions['period_end']);
    }

    if (isset($conditions['context']) && !empty($conditions['context']))
    {
      $qb->andwhere('v.context = :context')
        ->setParameter('context', $conditions['context']);
    }

    return $qb;
  }
}
