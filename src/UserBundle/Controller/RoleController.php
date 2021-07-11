<?php

namespace Rabble\UserBundle\Controller;

use Rabble\UserBundle\Entity\Role;
use Rabble\UserBundle\Form\RoleType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class RoleController extends AbstractController
{
    /**
     * @Security("is_granted('role.view')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@RabbleUser/Role/index.html.twig');
    }

    /**
     * @Security("is_granted('role.create')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $roleClass = $em->getRepository('Role')->getClassName();
        $role = new $roleClass();
        $form = $this->createForm(RoleType::class, $role)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'The role has been saved.');
            $em->persist($role);
            $em->flush();

            return $this->redirectToRoute('rabble_admin_role_index');
        }

        return $this->render('@RabbleUser/Role/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @ParamConverter("role", converter="implemented_entity", options={"entity" = "Role"})
     * @Security("(is_granted(role.getRoleName()) || is_granted('role.overrule')) && is_granted('role.edit')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Role $role)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(RoleType::class, $role)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'The role has been saved.');
            $em->flush();
            $form = $this->createForm(RoleType::class, $role);
        }

        return $this->render('@RabbleUser/Role/edit.html.twig', [
            'role' => $role,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @ParamConverter("role", converter="implemented_entity", options={"entity" = "Role"})
     * @Security("(is_granted(role.getRoleName()) || is_granted('role.overrule')) && is_granted('role.delete')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Role $role)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($role);
        $em->flush();

        return $this->redirectToRoute('rabble_admin_role_index');
    }
}
