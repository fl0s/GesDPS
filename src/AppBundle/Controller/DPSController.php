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
     * Page principale, liste des DPS en cours
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

                return $row;
            }
        );

        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(
            function ($query) use ($tableAlias)
            {
                // Récupération des DPS n'étant pas archivé
                $query->andWhere($tableAlias.".archive = 0");
            }
        );

        // Initiation du tableau
    	$grid = $this->get('grid');
    	$grid->setSource($source);
        $grid->setNoDataMessage( $this->get('translator')->trans('event.no_data') );
        $grid->isReadyForRedirect();

        // Colone d'édition des DPS
        $actionsColumn = new ActionsColumn('colAction', '');
        $grid->addColumn($actionsColumn);

        $editAction = new RowAction('', 'event-edit', false, '_self',
            array(
                'spanClass' => 'glyphicon glyphicon-pencil',
                'class'     => 'btn btn-warning btn-xs',
                'title'     => 'Editer'
                ));
        $editAction->setRouteParameters(array('id'));
        $editAction->setColumn('colAction');
        $grid->addRowAction($editAction);

        // Colonne d'archivage des DPS
        $editAction = new RowAction('', 'event-archive', true, '_self',
            array(
                'spanClass' => 'glyphicon glyphicon-folder-close',
                'class'     => 'btn btn-danger btn-xs',
                'title'     => 'Archiver'
                ));
        $editAction->setRouteParameters(array('id'));
        $editAction->setConfirmMessage( $this->get('translator')->trans('event.archive.confirm') );
        $editAction->setColumn('colAction');
        $grid->addRowAction($editAction);

        // Colonne de passage rapide des étapes
        $nextStepAction = new RowAction('next-step', 'event-next-step', false, '_self',
            array(
                'spanClass' => 'glyphicon glyphicon-ok',
                'class'     => 'btn btn-primary btn-xs'
                ));
        $nextStepAction->setRouteParameters(array('id'));
        $nextStepAction->setColumn('colAction');
        $nextStepAction->manipulateRender(
            function ($action, $row)
            {
                $getLastStep = $row->getEntity()->getLastStep();

                if (!is_null($getLastStep) && !is_null($getLastStep->getNextWorkflowStep())) {
                    $action->setTitle($getLastStep->getNextWorkflowStep()->getName());
                    $action->addRouteParameters(array('idNextStep' => $getLastStep->getNextWorkflowStep()->getId()));
                    return $action;
                } 

                return null;
            }
        );
        $grid->addRowAction($nextStepAction);

        return $grid->getGridResponse('home.html.twig');
    }

    /**
     * Liste des DPS étant archivé
     * @Route("/archive", name="event-archive-list")
     * @Secure(roles="ROLE_VIEWER")
     */
    public function listArchiveAction()
    {

        $source = new Entity('AppBundle:Event');
        $source->manipulateRow(
            function ($row) {
                $row->setField('coaFormated', $row->getEntity()->getFormatedCOA());
                $row->setField('status', $row->getEntity()->getLastStepName());

                return $row;
            }
        );

        $tableAlias = $source->getTableAlias();
        $source->manipulateQuery(
            function ($query) use ($tableAlias)
            {
                // Récupération des DPS archivé
                $query->andWhere($tableAlias.".archive = 1");
            }
        );

        $grid = $this->get('grid');
        $grid->setSource($source);
        $grid->setNoDataMessage( $this->get('translator')->trans('event.archive.no_data') );
        $grid->isReadyForRedirect();

        // Colonne de réouverture des DPS
        $editAction = new RowAction('', 'event-unarchive', false, '_self',
            array(
                'spanClass' => 'glyphicon glyphicon-folder-open',
                'class'     => 'btn btn-success btn-xs',
                'title'     => 'Ré-ouvrir'
                ));
        $editAction->setRouteParameters(array('id'));
        $editAction->setColumn('actionn');
        $grid->addRowAction($editAction);

        return $grid->getGridResponse('event/archive.html.twig');
    }

    /**
     * Vue de l'édition de DPS
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

            $flash->success('event.edit.success');

            return $this->redirect($this->generateUrl('event-index'));
        }

        return $this->render('event/edit.html.twig',
            array('eventForm' => $form->createView()));
    }

    /**
     * Action de passage rapide des étapes pour un DPS
     * @Route("/next-step/{id}/{idNextStep}", name="event-next-step")
     * @Secure(roles="ROLE_MANAGER")
     */
    public function nextStepAction(Request $request, $id, $idNextStep)
    {
        $em = $this->getDoctrine()->getManager();
        $flash = $this->get('braincrafted_bootstrap.flash');

        /**
         * @var \AppBundle\Entity\Event
         */
        $event = $em->getRepository('AppBundle:Event')->find($id);
        $nextStep = $em->getRepository('AppBundle:WorkflowStep')->find($idNextStep);

        if (!is_null($event) && !is_null($nextStep)) {
            $event->addStep($nextStep);
            $em->persist($event);
            $em->flush();

            $flash->success('event.next_step.success');
        } else {
            $flash->error('event.next_step.error');
        }

        return $this->redirect($this->generateUrl('event-index'));
    }

    /**
     * Action d'archivage d'un DPS
     * @Route("/action-archive/{id}", name="event-archive")
     * @Secure(roles="ROLE_MANAGER")
     */
    public function archiveEventAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $flash = $this->get('braincrafted_bootstrap.flash');

        $event = $em->getRepository('AppBundle:Event')->find($id);

        if (!is_null($event)) {
            $event->setArchive(true);
            $em->persist($event);
            $em->flush();

            $flash->success('event.archive.success');
        } else {
            $flash->error('event.archive.error');
        }

        return $this->redirect($this->generateUrl('event-index'));    
    }

    /**
     * Action de ré-ouverture d'un DPS archivé
     * @Route("/action-unarchive/{id}", name="event-unarchive")
     * @Secure(roles="ROLE_MANAGER")
     */
    public function unArchiveEventAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $flash = $this->get('braincrafted_bootstrap.flash');

        $event = $em->getRepository('AppBundle:Event')->find($id);

        if (!is_null($event)) {
            $event->setArchive(false);
            $em->persist($event);
            $em->flush();

            $flash->success('event.unarchive.success');
        } else {
            $flash->error('event.unarchive.error');
        }

        return $this->redirect($this->generateUrl('event-archive-list'));    
    }
}
