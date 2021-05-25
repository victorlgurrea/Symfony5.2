<?php

namespace App\Controller;

use App\Entity\Marcador;
use App\Form\MarcadorType;
use App\Repository\MarcadorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use stdClass;
use App\Entity\Categoria;

/**
 * @Route("/marcador")
 */
class MarcadorController extends AbstractController
{
    /**
     * @Route("/", name="marcador_index", methods={"GET"})
     */
    public function index(MarcadorRepository $marcadorRepository): Response
    {
        return $this->render('marcador/index.html.twig', [
            'marcadors' => $marcadorRepository->findAll(),
        ]);
    }

        /**
     * @Route("/listado", name="app_listado_marcadores")
     */
    public function list(MarcadorRepository $marcadorRepository)
    {

        $marcadores = $marcadorRepository->findAll();

        $output = [];
        foreach($marcadores as $marcador) {

            $obj = new stdClass();
            $obj->id = $marcador->getId();
            $obj->name = $marcador->getName();
            $obj->url = $marcador->getUrl();

            $category = $this->getDoctrine()->getRepository(Categoria::class)->find($marcador->getCategoria());

            $obj->category_name = $category->getName();
            $obj->color = $category->getColor();
            array_push($output, $obj);
        }
        
        echo json_encode($output);
        die();

    }

    /**
     * @Route("/new", name="marcador_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $marcador = new Marcador();
        $form = $this->createForm(MarcadorType::class, $marcador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($marcador);
            $entityManager->flush();
            $this->addFlash('success', 'marcador creado correctamente');
            return $this->redirectToRoute('marcador_index');
        }

        return $this->render('marcador/new.html.twig', [
            'marcador' => $marcador,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="marcador_show", methods={"GET"})
     */
    public function show(Marcador $marcador): Response
    {
        return $this->render('marcador/show.html.twig', [
            'marcador' => $marcador,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="marcador_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Marcador $marcador): Response
    {
        $form = $this->createForm(MarcadorType::class, $marcador);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'marcador actualizado correctamente');
            return $this->redirectToRoute('marcador_index');
        }

        return $this->render('marcador/edit.html.twig', [
            'marcador' => $marcador,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="marcador_delete", methods={"POST"})
     */
    public function delete(Request $request, Marcador $marcador): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marcador->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($marcador);
            $entityManager->flush();
            $this->addFlash('success', 'marcador eliminado correctamente');
            return $this->redirectToRoute('marcador_index');
        }

        $this->addFlash('danger', 'marcador no eliminado');
            return $this->redirectToRoute('marcador_index');
        
    }

     /**
     * @Route("/{id}/remove", name="marcador_remove", methods={"POST","GET"})
     */
    public function remove(Request $request, Marcador $marcador): Response
    {
  

            $entityManager = $this->getDoctrine()->getManager();
            $marcador = $this->getDoctrine()->getRepository(Marcador::class)->findOneBy(['id' => $request->request->get('id')]);

            $entityManager->remove($marcador);
            $entityManager->flush();
            echo json_encode(true);
            die;
        
    }
}
