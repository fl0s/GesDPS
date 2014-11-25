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
        $stepNames = array('demande', 'devis', 'facture');
        foreach ($stepNames as $stepName) {
            $step = new WorkflowStep();
            $step->setName($stepName);

            $manager->persist($step);

            $this->addReference('step_'.$stepName, $step);
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