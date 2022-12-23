<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Menu;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'Menu')]
    public function index()
    {
        // listing
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository(Menu::class);
        $menus = $repo->findAll();

        return $this->render('menu/index.html.twig', [
            'menus' => $menus,
        ]);
    }

    #[Route('/menu/ajouter', name:"MenuAjouter")]
    public function addM() {
        $new_menu = new Menu();
        $f_add_m = $this->createFormBuilder($new_menu)
        ->add("titre", TextType::class)
        ->add("nbr_calories", IntegerType::class)
        ->add("price", NumberType::class)
        ->add("Add", SubmitType::class);
        $form_add_m = $f_add_m->getForm();
        $request = Request::createFromGlobals();
        $form_add_m->handleRequest($request);
        if ($form_add_m->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($new_menu);
            $em->flush();
            return $this->redirectToRoute('Menu');
        }
        return $this->render('menu/ajouter/index.html.twig', [
            'f_add_m' => $form_add_m->createView()
        ]);
    }
    
    #[Route('/menu/delete/{id}', name:"MenuDelete")]
    public function delete(Request $request, $id) {
        $c = $this -> getDoctrine()
        ->getRepository(Menu::class)
        ->find($id);
        if (!$c) {
            throw $this->createNotFoundException(
                "No menu found for id".$id
            );
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($c);

        $entityManager->flush();
        return $this->redirectToRoute('Menu');
    }
}
