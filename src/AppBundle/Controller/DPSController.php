<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use APY\DataGridBundle\Grid\Source\Entity;
use AppBundle\Entity\Event;
use AppBundle\Form\EventType;
use Symfony\Component\HttpFoundation\Request;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use JMS\SecurityExtraBundle\Annotation\Secure;

class DPSController extends Controller
{
    /**
     * @Route("/", name="event-index")
     * @Secure(roles="ROLE_VIEWER")
     */
    public function indexAction()
    {

    	$source = new Entity('AppBundle:Event');
        $source->manipulateRow(
            function ($row) {
                $row->setField('coaFormated', $row->getEntity()->getFormatedCOA());
                $row->setField('status', $row->getEntity()->getLastStepName());

                $lastStep = $row->getEntity()->getLastStep();

                if (!is_null($lastStep) && $lastStep->getColor() != "") {
                    $row->setColor($row->getEntity()->getLastStep()->getColor());
                } else {
                    $row->setClass('danger');
                }

                return $row;
            }
        );

    	$grid = $this->get('grid');

    	$grid->setSource($source);

        $actionsColumn = new ActionsColumn('actionn', '');
        $grid->addColumn($actionsColumn);

        $editAction = new RowAction('', 'event-edit', false, '_self',
            array(
                'spanClass' => 'glyphicon glyphicon-pencil',
                'class'     => 'btn btn-warning btn-xs'
                ));
        $editAction->setRouteParameters(array('id'));
        $editAction->setColumn('actionn');
        $grid->addRowAction($editAction);

    	$grid->isReadyForRedirect();

    	return $this->render('home.html.twig', array('grid' => $grid));
    }

    /**
     * @Route("/edit/{id}", name="event-edit")
     * @Secure(roles="ROLE_MANAGER")
     */
    public function editEventAction(Request $request, $id = null)
    {
        $em = $this->getDoctrine()->getManager();
        $flash = $this->get('braincrafted_bootstrap.flash');

        /**
         * @var \AppBundle\Entity\Event
         */
        $event = null;


        if (!is_null($id)) {
            $event = $em->getRepository('AppBundle:Event')->find($id);
        } else {
            $event = new Event();
        }

        $form = $this->createForm(new EventType(), $event);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (empty($event->getCOA())) {

                $coa = $em->getRepository("AppBundle:Event")->getLastCOA();
                $event->setCoa($coa +1);
                $event->setCoaYear(date('Y'));
            }

            $em->persist($event);
            $em->flush();

            $flash->success('L\'évènement à bien été enregistré!');

            return $this->redirect($this->generateUrl('event-index'));
        }

        return $this->render('event/edit.html.twig',
            array('eventForm' => $form->createView()));
    }
}
