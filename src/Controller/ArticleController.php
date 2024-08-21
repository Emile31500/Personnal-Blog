<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ArticleController extends AbstractController
{
    #[Route('/articles/page/{index_page}',  name: 'app_article_list',)]
    public function articleList(int $index_page, ArticleRepository $articleRepository): Response
    {
        
        $articles = $articleRepository->findPublishedArticle($index_page-1);

        return $this->render('article/list.html.twig', [
            'controller_name' => 'Articles',
            'articles' => $articles
        ]);
    }

    #[Route('/articles/{id}/detail',  name: 'app_article_detail')]
    public function articleDetail(Article $article): Response
    {
        return $this->render('article/detail.html.twig', [
            'article' => $article
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
    #[Route('/articles/create',  name: 'app_article_create',)]
    public function articleCreate(): Response
    {
        return $this->render('article/create.html.twig', [
            'controller_name' => 'Nouvel article',
        ]);
    }

    #[Route('/api/articles/{id}', methods: 'GET',  name: 'api_article_detail')]
    public function getDetailArticle(Article $article, Security $security, SerializerInterface $serializer): Response
    {
        if (!$security->isGranted('ROLE_ADMIN') || !$article->getIsPublished()) {

            $jsonArticles = $serializer->serialize($article, 'json');
            return new Response($jsonArticles, Response::HTTP_OK, ['Content-Type' => 'application/json']);

        }

        return new Response('', Response::HTTP_NOT_FOUND, ['Content-Type' => 'application/json']);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/unpublished/articles/', methods: 'GET',  name: 'api_article_unpublished',)]
    public function getPublishedArtocme(ArticleRepository $articleRepository, SerializerInterface $serializer): Response
    {

        $articles = $articleRepository->findUnpublishedArticle();
        $jsonArticles = $serializer->serialize($articles, 'json');

        return new Response($jsonArticles, Response::HTTP_OK, ['Content-Type' => 'application/json']);

    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/api/articles/', methods: 'POST',  name: 'api_article_create',)]
    public function postArticle(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $article = $serializer->deserialize($request->getContent(), Article::class, 'json');

        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            return new Response($errorsString, Response::HTTP_BAD_REQUEST);
        }
        
        $entityManager->persist($article);
        $entityManager->flush();
 
        $jsonArticle = $serializer->serialize($article, 'json');
        return new Response($jsonArticle, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }
}
