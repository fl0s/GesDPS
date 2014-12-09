<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * WorflowStep
 *
 * @ORM\Table()
 * @ORM\Entity
 * @GRID\Source(columns="id, order, name, color", filterable=false)
 */
class WorkflowStep
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     * @GRID\Column(title="step.name")
     */
    protected $name;

    /**
     * @var Event[]
     * 
     * @ORM\ManyToMany(targetEntity="Event", mappedBy="steps")
     */
    protected $events;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", nullable=true)
     * @GRID\Column(title="step.color")
     */
    protected $color;

    /**
     * @var int
     *
     * @ORM\Column(name="step_order", type="integer")
     * @GRID\Column(title="step.order")
     */
    protected $order;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return WorkflowStep
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Add events
     *
     * @param \AppBundle\Entity\Event $events
     * @return WorkflowStep
     */
    public function addEvent(\AppBundle\Entity\Event $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \AppBundle\Entity\Event $events
     */
    public function removeEvent(\AppBundle\Entity\Event $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set color
     *
     * @param string $color
     * @return WorkflowStep
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return WorkflowStep
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
}
