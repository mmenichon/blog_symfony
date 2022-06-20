<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function index(CategoryRepository $repository): Response
    {
        $category = $repository->findAll();
        return $this->render('category/index.html.twig', [
            'categories' => $category,
        ]);
    }

    /**
     * @Route("/addcat", name="addcat")
     */
    public function ajoutCat(Category $category = null, Request $request, EntityManagerInterface $manager):Response
    {
        if (!$category) {
            $category = new Category();
        }

        $form =$this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();

            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('app_category');
        }

        return $this->renderForm('category/addCategory.html.twig', [
            'form' => $form,
        ]);

    }
}
