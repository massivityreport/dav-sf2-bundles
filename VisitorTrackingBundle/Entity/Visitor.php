<?php

namespace Daveudaimon\VisitorTrackingBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Daveudaimon\VisitorTrackingBundle\Repository\VisitorRepository")
 * @ORM\Table(name="visitor")
 * @ORM\HasLifecycleCallbacks
 */
class Visitor
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $ip;

    /**
     * @ORM\Column(type="string", length=1023)
     */
    protected $referer;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $context;

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
      $this->created = new \DateTime("now");
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ip
     *
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set referer
     *
     * @param string $referer
     * @return Visitor
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;

        return $this;
    }

    /**
     * Get referer
     *
     * @return string
     */
    public function getReferer()
    {
        return $this->referer;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        if (preg_match('/google.*(?:&|\?)q=([^&]+)(?:$|&)/', $this->referer, $matches))
        {
            return urldecode($matches[1]);
        }

        return '';
    }

    /**
     * Set context
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }
}
