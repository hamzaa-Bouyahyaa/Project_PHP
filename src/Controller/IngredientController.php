<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Ingredient;
use App\Entity\Menu;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'Ingredient')]
    public function index(): Response
    {
        // listing
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository(Ingredient::class);
        $ingredients = $repo->findAll();

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    #[Route('/ingredient/ajouter', name:"IngredientAjouter")]
    public function create() {
        $new = new Ingredient();
        $f_add = $this->createFormBuilder($new)
        ->add("libelle", TextType::class)
        ->add("quantite", IntegerType::class)
        ->add("cout", NumberType::class)
        ->add("Menu", EntityType::class, [
            "class" => Menu::class,
            "choice_label" => "id",
        ])
        ->add("create", SubmitType::class);
        $form_add = $f_add->getForm();
        $request = Request::createFromGlobals();
        $form_add->handleRequest($request);
        if ($form_add->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($new);
            $em->flush();
            return $this->redirectToRoute('Menu');
        }
        return $this->render('ingredient/ajouter/index.html.twig', [
            'f_add' => $form_add->createView()
        ]);
    }

    #[Route('/ingredient/{id}', name:"IngredientDetails")]
    public function show(Request $request, $id) {
        $c = $this->getDoctrine()
        ->getRepository(Ingredient::class)
        ->find($id);
        if (!$c) {
            throw $this->createNotFoundException(
                "No ingredient found for id".$id
            );
        }
        return $this->render('ingredient/show.html.twig', [
            'c' => $c,
        ]);
    }
}
