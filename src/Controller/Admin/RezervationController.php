<?php

namespace App\Controller\Admin;

use App\Entity\Admin\Rezervation;
use App\Form\Admin\RezervationType;
use App\Repository\Admin\RezervationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/rezervation")
 */
class RezervationController extends AbstractController
{
    /**
     * @Route("/{slug}", name="admin_rezervation_index", methods={"GET"})
     */
    public function index($slug,RezervationRepository $rezervationRepository): Response
    {
        $rezervations = $rezervationRepository->getrezervations($slug);
        return $this->render('admin/rezervation/index.html.twig', [
            'rezervations' => $rezervations,
        ]);
    }

    /**
     * @Route("/new", name="admin_rezervation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $rezervation = new Rezervation();
        $form = $this->createForm(RezervationType::class, $rezervation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($rezervation);
            $entityManager->flush();

            return $this->redirectToRoute('admin_rezervation_index');
        }

        return $this->render('admin/rezervation/new.html.twig', [
            'rezervation' => $rezervation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="admin_rezervation_show", methods={"GET"})
     */
    public function show($id,RezervationRepository $rezervationRepository): Response
    {
        $rezervation = $rezervationRepository->getrezervation($id);
        return $this->render('admin/rezervation/show.html.twig', [
            'rezervation' => $rezervation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_rezervation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Rezervation $rezervation): Response
    {
        $form = $this->createForm(RezervationType::class, $rezervation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $status = $form['status']->getData();

            return $this->redirectToRoute('admin_rezervation_index',['slug'=>$status]);
        }

        return $this->render('admin/rezervation/edit.html.twig', [
            'rezervation' => $rezervation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_rezervation_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Rezervation $rezervation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rezervation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($rezervation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_rezervation_index');
    }
}
