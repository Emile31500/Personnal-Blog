<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/articles/page/{index_page}',  name: 'app_article_list',)]
    public function articleList(int $index_page,ArticleRepository $articleRepository): Response
    {
        
        $articles = $articleRepository->findByPublishedArticle($index_page-1);

        return $this->render('article/list.html.twig', [
            'controller_name' => 'Articles',
            'articles' => $articles
        ]);
    }

    #[Route('/articles/{id_article}',  name: 'app_article_detail',)]
    public function articleDetail(): Response
    {
        return $this->render('article/detail.html.twig', [
            'controller_name' => $article->getName(),
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/articles/{id_article}/edition',  name: 'app_article_edit',)]
    public function articleEdition(): Response
    {
        return $this->render('article/edit.html.twig', [
            'controller_name' => 'Edition',
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/create/articles/',  name: 'app_article_create',)]
    public function articleCreat(): Response
    {
        return $this->render('article/edit.html.twig', [
            'controller_name' => 'Nouvel article',
        ]);
    }
}
