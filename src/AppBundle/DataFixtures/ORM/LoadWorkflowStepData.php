<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\WorkflowStep;

class LoadWorkflowStepData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $steps = array(
            array('demande', '#c0392b', 1),
            array('devis', '#e67e22', 2),
            array('convention', '#16a085', 3),
            array('facture', '#27ae60', 4)
        );


        foreach ($steps as $step) {
            $wStep = new WorkflowStep();
            $wStep->setName($step[0]);
            $wStep->setColor($step[1]);
            $wStep->setOrder($step[2])

            $manager->persist($wStep);

            $this->addReference('step_'.$wStep->getName(), $wStep);
        }
        
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}