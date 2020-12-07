<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Entity\Comment;
use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecipeController extends AbstractController
{
    /**
     * @Route("/recipe", name="recipe_index", methods="GET|POST")
     */
    public function index(
        RecipeRepository $recipeRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $newRecipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $newRecipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($newRecipe);
            $entityManager->flush();

            return $this->redirectToRoute('recipe_index');
        }

        $recipes = $recipeRepository->findAll();

        return $this->render('recipe/index.html.twig', [
            'recipes' => $recipes,
            'newRecipe' => $newRecipe,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/recipe/{id}", requirements={"id"="\d+"}, name="recipe_show", methods="GET|POST")
     */
    public function show(
        Recipe $recipe,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $newComment = new Comment();
        $form = $this->createForm(CommentType::class, $newComment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newComment->setRecipe($recipe);

            $entityManager->persist($newComment);
            $entityManager->flush();

            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render('recipe/show.html.twig', [
            'recipe' => $recipe,
            'newComment' => $newComment,
            'form' => $form->createView()
        ]);
    }
}
