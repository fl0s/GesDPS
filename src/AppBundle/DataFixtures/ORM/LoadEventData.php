<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Event;

class LoadEventData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = $this->getReference('user_admin');
        $step = $this->getReference('step_demande');

        $event = new Event();
        $event->setName('evenement1');
        $event->setDate(new \DateTime());
        $event->setManager($user);
        $event->setCoa(1);
        $event->setCoaYear(2014);
        $event->addStep($step);

        $manager->persist($event);
        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 3;
    }
}