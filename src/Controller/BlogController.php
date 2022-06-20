<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchFormType;
use App\Repository\ArticleRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function allArticles(ArticleRepository $articleRepository, Request $request): Response
    {
        //$articles = $articleRepository->findAll();
        //$categories = $categoryRepository->findAll();

        // code d'Axel
        $data = new SearchData();
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        $articles = $articleRepository->findSearch($data);
        //

        return $this->render('blog/index.html.twig', [
            'articles' => $articles,
            //'categories' => $categories,
            'form' => $form->createView(),
        ]);
    }
}
