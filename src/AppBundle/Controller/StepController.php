<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Component\HttpFoundation\Request;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use AppBundle\Form\StepType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use AppBundle\Entity\WorkflowStep;

class StepController extends Controller
{
	/**
     * @Route("/step", name="step-index")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
    	$source = new Entity('AppBundle:WorkflowStep');

        $source->manipulateRow(
            function ($row) {
                $stepColor = $row->getEntity()->getColor();

                if (!is_null($stepColor) && $stepColor != "") {
                    $row->setColor($stepColor);
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

        // Edit
        $editAction = new RowAction('', 'step-edit', false, '_self',
            array(
                'spanClass' => 'glyphicon glyphicon-pencil',
                'class'     => 'btn btn-warning btn-xs'
                ));
        $editAction->setRouteParameters(array('id'));
        $editAction->setColumn('actionn');
        $grid->addRowAction($editAction);

        //Delete
        $deleteAction = new RowAction('', 'step-delete', false, '_self',
            array(
                'spanClass' => 'glyphicon glyphicon-trash',
                'class'     => 'btn btn-danger btn-xs'
                ));
        $deleteAction->setRouteParameters(array('id'));
        $deleteAction->setColumn('actionn');
        $grid->addRowAction($deleteAction);

    	$grid->isReadyForRedirect();

    	return $this->render('step/home.html.twig', array('grid' => $grid));
    }

    /**
     * @Route("/step/edit/{id}", name="step-edit", defaults={ "id" = null })
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editAction(Request $request, WorkflowStep $step = null)
    {
    	$em = $this->getDoctrine()->getManager();
    	$flash = $this->get('braincrafted_bootstrap.flash');

        if (is_null($step)) {
            $step = new WorkflowStep();
        }

    	$form = $this->createForm(new StepType(), $step);

    	$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($step);
            $em->flush();

            $flash->success($this->get('translator')->trans('step.edit.success'));

            return $this->redirect($this->generateUrl('step-index'));
        }

    	return $this->render("step/edit.html.twig", array("stepForm" => $form->createView()));
    }

    /**
     * Delete WorkflowStep by Id
     * @Route("/step/delete/{id}", name="step-delete")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction(Request $request, WorkflowStep $step)
    {
        $em = $this->getDoctrine()->getManager();
        $flash = $this->get('braincrafted_bootstrap.flash');

        $em->remove($step);
        $em->flush();

        $flash->alert($this->get('translator')->trans('step.delete.deleted'));

        return $this->redirect($this->generateUrl('step-index'));
    }

    
}