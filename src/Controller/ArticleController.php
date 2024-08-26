<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManager;
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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
        if ($security->isGranted('ROLE_ADMIN') || $article->getIsPublished()) {

            $jsonArticles = $serializer->serialize($article, 'json');
            return new Response($jsonArticles, Response::HTTP_OK, ['Content-Type' => 'application/json']);

        }

        return new Response('', Response::HTTP_NOT_FOUND, ['Content-Type' => 'application/json']);
    }

    #[Route('/api/unpublished/articles/', methods: 'GET',  name: 'api_article_unpublished',)]
    public function getUnpublishedArticLe(ArticleRepository $articleRepository, Security $security, SerializerInterface $serializer): Response
    {

        if (!$security->isGranted('ROLE_ADMIN')) {

            return new Response('', Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);
            
        } else {

            $articles = $articleRepository->findUnpublishedArticle();
            $jsonArticles = $serializer->serialize($articles, 'json');

            return new Response($jsonArticles, Response::HTTP_OK, ['Content-Type' => 'application/json']);

        }

    }

    #[Route('/api/articles/{id}', methods: 'DELETE',  name: 'api_delete_article',)]
    public function deleteArticLe(Article $article, EntityManagerInterface $em, Security $security): Response
    {

        if (!$security->isGranted('ROLE_ADMIN')) {

            return new Response('', Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);
            
        } else {

            $em->remove($article);
            $em->flush();

            return new Response('', Response::HTTP_NO_CONTENT, ['Content-Type' => 'application/json']);

        }

    }

    #[Route('/api/articles/', methods: 'POST',  name: 'api_article_create',)]
    public function postArticle(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator, Security $security): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {

            $article = $serializer->deserialize($request->getContent(), Article::class, 'json');
            $errors = $validator->validate($article);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                return new Response($errorsString, Response::HTTP_BAD_REQUEST);
            }
            
            $em->persist($article);
            $em->flush();
    
            $jsonArticle = $serializer->serialize($article, 'json');
            return new Response($jsonArticle, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);

        } else {

            return new Response('', Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);

        }
    }

    #[Route('/api/articles/{id}', methods: 'PUT',  name: 'api_article_edit',)]
    public function editArticle(Article $article, Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator, Security $security): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {

            $article = $serializer->deserialize($request->getContent(), Article::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $article]);
            $errors = $validator->validate($article);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                return new Response($errorsString, Response::HTTP_BAD_REQUEST);
            }
            
            $em->persist($article);
            $em->flush();
    
            $jsonArticle = $serializer->serialize($article, 'json');
            return new Response($jsonArticle, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);

        } else {

            return new Response('', Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);

        }
    }
}
