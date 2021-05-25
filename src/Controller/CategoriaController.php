<?php

namespace App\Controller;

use App\Entity\Categoria;
use App\Repository\CategoriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Catch_;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

    /**
     * @Route("/categoria")
     */

class CategoriaController extends AbstractController
{
    /**
     * @Route("/index", name="app_categoria")
     */
    public function index(): Response
    {
        return $this->render('categoria/index.html.twig', []
        );
    }

    /**
     * @Route("/listado", name="app_listado_categoria")
     */
    public function list(CategoriaRepository $categoriaRepository)
    {

        $categorias = $categoriaRepository->findAll();
        $output = [];
        foreach($categorias as $category) {
            $obj = new stdClass();
            $obj->id = $category->getId();
            $obj->name = $category->getName();
            $obj->color = $category->getColor();
            array_push($output, $obj);
        }
        
        echo json_encode($output);
        die();

    }


    /**
     * @Route("/nueva", name="app_add_categoria")
     */
    public function add_category(CategoriaRepository $categoriaRepository,EntityManagerInterface $entityManager, Request $request): Response
    {

        $categoria = new Categoria();
        if($this->isCsrfTokenValid('categoria', $request->request->get('_token'))) {
            $name = $request->request->get('name', null);
            $color = $request->request->get('color', null);
            $id = $request->request->get('id', null);

            if($name && $color && !$id){
                $categoria->setName($name);
                $categoria->setColor($color);
                $entityManager->persist($categoria);
                $entityManager->flush();
                $this->addFlash('success', 'categoría creada correctamente');
            } else {
                if(! $name){
                    $this->addFlash('danger', 'El campo nombre es obligatorio');
                }
                if(! $color) {
                    $this->addFlash('danger', 'El campo color es obligatorio');
                }

                if( $id) {
                    $category = $this->getDoctrine()->getRepository(Categoria::class)->find($id);
                    $category->setName($name);
                    $category->setColor($color);
                    $entityManager->persist($category);
                    $entityManager->flush();
                    $this->addFlash('success', 'categoría actualizada correctamente');
                }
                
            }
            
            return $this->redirectToRoute('app_categoria');
        }

        return $this->redirectToRoute('app_categoria');

    }

     /**
     * @Route("/eliminar", name="app_remove_categoria")
     */
    public function remove(CategoriaRepository $categoriaRepository,EntityManagerInterface $entityManager, Request $request): Response
    {
        $id = $request->request->get('id_remove', null);
        $category = $this->getDoctrine()->getRepository(Categoria::class)->find($id);
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'categoría eliminada correctamente');
        return $this->redirectToRoute('app_categoria');
    }
}
