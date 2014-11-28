<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use APY\DataGridBundle\Grid\Source\Entity;
use Symfony\Component\HttpFoundation\Request;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use AppBundle\Form\UserType;
use JMS\SecurityExtraBundle\Annotation\Secure;

class UserController extends Controller
{
	/**
     * @Route("/users", name="user-index")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function indexAction()
    {
    	$source = new Entity('AppBundle:User');

    	$grid = $this->get('grid');

    	$grid->setSource($source);

        $grid->getColumn('enabled')->setTitle('user.enabled');

    	$grid->getColumn('enabled')->manipulateRenderCell(
		    function($value, $row, $router) {
		        return ($value?"enable":"disable");
		    }
		);

		$actionsColumn = new ActionsColumn('actionn', '');
        $grid->addColumn($actionsColumn);

        $editAction = new RowAction('', 'user-edit', false, '_self',
            array(
                'spanClass' => 'glyphicon glyphicon-pencil',
                'class'     => 'btn btn-warning btn-xs'
                ));
        $editAction->setRouteParameters(array('id'));
        $editAction->setColumn('actionn');
        $grid->addRowAction($editAction);

    	$grid->isReadyForRedirect();

    	return $this->render('user/home.html.twig', array('grid' => $grid));
    }

    /**
     * @Route("/user/edit/{id}", name="user-edit")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editAction(Request $request, $id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$flash = $this->get('braincrafted_bootstrap.flash');

    	$user = $em->getRepository("AppBundle:User")->find($id);

    	$form = $this->createForm(new UserType(), $user);

    	$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($user);
            $em->flush();

            $flash->success('L\'utilisateur à bien été enregistré!');

            return $this->redirect($this->generateUrl('user-index'));
        }

    	return $this->render("user/edit.html.twig", array("userForm" => $form->createView()));
    }
}