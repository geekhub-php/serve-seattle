<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Admin;
use AppBundle\Form\AdminType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Class DefaultController.
 *
 * @Route("/admin", name="admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="admin_info")
     * @Template("@App/Admin/index.html.twig")
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $admin = $em->getRepository(Admin::class)->findOneBy(['id' => $this->getUser()->getId()]);
        $editForm = $this->createForm(AdminType::class, $admin)
            ->add('Save', SubmitType::class, array(
                'attr' => array('class' => 'btn btn-primary'),
            ));
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted()) {
            if ($editForm->isValid()) {
                $em->persist($admin);
                $em->flush();
            }
        }

        return [
            'admin' => $admin,
            'editForm' => $editForm->createView(),
        ];
    }
}
