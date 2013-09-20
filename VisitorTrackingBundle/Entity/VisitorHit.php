<?php
namespace daveudaimon\VisitorTrackingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="daveudaimon\VisitorTrackingBundle\Repository\VisitorHitRepository")
 * @ORM\Table(name="visitor_hit")
 * @ORM\HasLifecycleCallbacks
 */
class VisitorHit
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

   /**
     * @ORM\ManyToOne(targetEntity="Visitor")
     * @ORM\JoinColumn(name="visitor_id", referencedColumnName="id")
     */
    protected $visitor;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $target;

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
     * Set visitor
     *
     * @param daveudaimon\VisitorTrackingBundle\Entity\Visitor $visitor
     */
    public function setVisitor(\daveudaimon\VisitorTrackingBundle\Entity\Visitor $visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * Get visitor
     *
     * @return daveudaimon\VisitorTrackingBundle\Entity\Visitor
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Set target
     *
     * @param integer $target
     */
    public function setTarget($target)
    {
        $this->target = $target;
    }

    /**
     * Get target
     *
     * @return integer
     */
    public function getTarget()
    {
        return $this->target;
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

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->created->format('Y-m-d H:i:s');
    }
}
