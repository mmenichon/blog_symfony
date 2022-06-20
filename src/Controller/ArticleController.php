<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleFormType;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ArticleController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/article/{id}", name="article")
     */
    public function showArticle(Article $article): Response
    {
        // afficher l'article
        $title = $article->getTitle();
        $content = $article->getDescription();
        $date = $article->getCreateAt();
        // afficher la catégorie
        $category = $article->getCategories();
        // afficher les commentaires
        $comments = $article->getComments();

        return $this->render('article/index.html.twig', [
            'title' => $title,
            'content' => $content,
            'date' => $date,
            'categories' => $category,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/newarticle", name="newarticle")
     * @Route("/edit/{id}", name="editarticle")
     */
    public function newArticle (Article $article = null, Request $request, EntityManagerInterface $manager):Response
    {
        if (!$article) {
            $article = new Article();

            // récupération de l'ID de l'utilisateur connectéarticle
            $user = $this->security->getUser();
            /** @var \App\Entity\User $user */
            $article->setUsers($user);
            // génération automatique de la date et de l'heure
            $article->setCreateAt(new \DateTime());
        }

        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $manager->persist($article);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->renderForm('article/addArticle.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="deletearticle")
     */
    public function delete (Article $article, EntityManagerInterface $manager){
        $manager->remove($article);
        $manager->flush();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/comment/{id}", name="newcomment")
     */
    public function addComment(Article $article, Request $request, EntityManagerInterface $manager):Response
    {
        // création d'un nouveau commentaire
        $comment = new Comment();

        // récupération de l'auteur
        $user = $this->security->getUser();
        /** @var \App\Entity\User $user */
        $comment->setAuthor($user);

        // récupération de l'article
        $comment->setArticle($article);

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();

            $manager->persist($comment);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->renderForm('article/addComment.html.twig', [
            'form' => $form,
        ]);
    }
}
