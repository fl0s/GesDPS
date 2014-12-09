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
            array('demande', '#d9534f', 1),
            array('devis', '#f0ad4e', 2),
            array('facture', '#5cb85c', 3)
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