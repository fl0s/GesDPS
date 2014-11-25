<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Group;

class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $data = array(
            "sa" => array("Super Administrateur", array("ROLE_SUPER_ADMIN")),
            "a" => array("Administrateur", array("ROLE_ADMIN")),
            "u" => array("Utilisateur", array("ROLE_USER")),
            "l" => array("Lecteur", array("ROLE_VIEWER")),
            );

        foreach ($data as $key => $values) {
            $group = new Group($values[0], $values[1]);

            $manager->persist($group);
            $this->addReference('group_'.$key, $group);
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