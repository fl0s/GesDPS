<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use APY\DataGridBundle\Grid\Mapping as GRID;

/**
 * Event
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\EventRepository")
 * @GRID\Source(columns="id, name, date, manager.username, coaFormated, status")
 * @GRID\Column(id="coaFormated", title="event.coa", size="9", type="text", filterable=false)
 * @GRID\Column(id="status", title="event.step", type="text", filterable=false)
 */
class Event
{
    const CHAR_COA = "/";


    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @GRID\Column(filterable=false)
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     * @GRID\Column(filterable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text")
     * @GRID\Column(filterable=false)
     */
    protected $comment = "";

    /**
     * @var DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     * @GRID\Column(filterable=false)
     */
    protected $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="coa", type="integer")
     */
    protected $coa;

    /**
     * @var integer
     *
     * @ORM\Column(name="coa_year", type="integer")
     */
    protected $coa_year;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="events")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id")
     * @GRID\Column(field="manager.username", title="Manager", filterable=false)
     */
    protected $manager;

    /**
     * @var WorkflowStep[]
     * 
     * @ORM\ManyToMany(targetEntity="WorkflowStep", inversedBy="events")
     * @ORM\JoinTable(name="events_steps")
     */
    protected $steps;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->steps = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Event
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
     * Set date
     *
     * @param \DateTime $date
     * @return Event
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set manager
     *
     * @param \AppBundle\Entity\User $manager
     * @return Event
     */
    public function setManager(\AppBundle\Entity\User $manager = null)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return \AppBundle\Entity\User 
     */
    public function getManager()
    {
        return $this->manager;
    }

    /**
     * Add steps
     *
     * @param \AppBundle\Entity\WorkflowStep $steps
     * @return Event
     */
    public function addStep(\AppBundle\Entity\WorkflowStep $steps)
    {
        $this->steps[] = $steps;

        return $this;
    }

    /**
     * Remove steps
     *
     * @param \AppBundle\Entity\WorkflowStep $steps
     */
    public function removeStep(\AppBundle\Entity\WorkflowStep $steps)
    {
        $this->steps->removeElement($steps);
    }

    /**
     * Get steps
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSteps()
    {
        return $this->steps;
    }

    public function getFormatedCOA()
    {
        return $this->coa.Event::CHAR_COA.$this->coa_year;
    }

    /**
     * Set coa
     *
     * @param integer $coa
     * @return Event
     */
    public function setCoa($coa)
    {
        $this->coa = $coa;

        return $this;
    }

    /**
     * Get coa
     *
     * @return integer 
     */
    public function getCoa()
    {
        return $this->coa;
    }

    /**
     * Set coa_year
     *
     * @param integer $coaYear
     * @return Event
     */
    public function setCoaYear($coaYear)
    {
        $this->coa_year = $coaYear;

        return $this;
    }

    /**
     * Get coa_year
     *
     * @return integer 
     */
    public function getCoaYear()
    {
        return $this->coa_year;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return Event
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    public function getLastStep()
    {
        $result = null;

        foreach ($this->steps as $key => $step) {
            if (is_null($result) || $step->getId() > $result->getId()) {
                $result = $step;
            }
        }

        return (is_null($result)?null:$result);
    }

    public function getLastStepName()
    {
        $lastStep = $this->getLastStep();

        return (is_null($lastStep)?"":$lastStep->getName());
    }
}
