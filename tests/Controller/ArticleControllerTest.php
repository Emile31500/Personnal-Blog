<?php

namespace Tests\Controller;

use App\Entity\Article;
use PHPUnit\Framework\TestCase;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use PHPUnit\Framework\Attibutes\Test;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ArticleControllerTest extends WebTestCase
{
    /**
     * @group unauth
     * @group get
     * @group 200
     */
    public function testGetArticleDetail(): void
    {

        $client = static::createClient();
        $articleRepository = static::getContainer()->get(ArticleRepository::class);
        $articles = $articleRepository->findPublishedArticle(1);

        $i = 0;

        do {

            $article = $articles[$i];
            $i++;

        } while ($article->getIsPublished() === false);

        $id = $article->getId();
        $articleRepo = $articleRepository->findOneById($id);
        $client->request('GET', '/api/articles/'.$id);
       
        $articleApi = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertSame($articleRepo->getId(), $articleApi['id']);

    }

    /**
     * @group unauth
     * @group get
     * @group 403   
     */
    public function testUnauthGetUnpublishedArticles(): void
    {

        $client = static::createClient();
        $client->request('GET', '/api/unpublished/articles/');
      
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

    }

    /**
     * @group authUser
     * @group get
     * @group 403  
     */
    public function testAuthUserGetUnpublishedArticles(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('user@emile.blog');
        $client->loginUser($user);

        $client->request('GET', '/api/unpublished/articles/');
        
        // $articlesApi = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

    }

    /**
     * @group authAdmin
     * @group get
     * @group 200
     */
    public function testAuthAdminGetUnpublishedArticles(): void
    {

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneByEmail('admin@emile.blog');
        $client->loginUser($admin);

        $client->request('GET', '/api/unpublished/articles/');

        $articlesApi = json_decode($client->getResponse()->getContent(), true);

        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        foreach ($articlesApi as $article) {

            $this->assertSame(false, $articlesApi[0]['isPublished']);

        }
    }

    /**
     * @group unAuth
     * @group delete
     * @group 403
     */
    public function testUnauthDeleteArticles(): void
    {

        $client = static::createClient();
        $articleRepository = static::getContainer()->get(ArticleRepository::class);
        $count = $articleRepository->findCount();
        $randomOffset = random_int(0, $count - 1);
        $articles = $articleRepository->findAll();
        $article = $articles[$randomOffset];

        $id = $article->getId();

        $client->request('DELETE', '/api/articles/'.$id);

        $undeltedArticle = $articleRepository->findOneById($id);

        $this->assertSame($article, $undeltedArticle);
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
        
    }

    /**
     * @group authUser
     * @group delete
     * @group 403
     */
    public function testAuthUserDeleteArticles(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $articleRepository = static::getContainer()->get(ArticleRepository::class);
        $count = $articleRepository->findCount();
        $randomOffset = random_int(0, $count - 1);
        $articles = $articleRepository->findAll();
        $article = $articles[$randomOffset];

        $id = $article->getId();
        $user = $userRepository->findOneByEmail('user@emile.blog');
        $client->loginUser($user);

        $client->request('DELETE', '/api/articles/'.$id);

        $undeltedArticle = $articleRepository->findOneById($id);

        $this->assertSame($article, $undeltedArticle);
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * @group authAdmin
     * @group delete
     * @group 204
     */
    public function testAuthAdminDeleteArticles(): void
    {

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $articleRepository = static::getContainer()->get(ArticleRepository::class);
        $admin = $userRepository->findOneByEmail('admin@emile.blog');
        $count = $articleRepository->findCount();
        $randomOffset = random_int(0, $count - 1);
        $articles = $articleRepository->findAll();
        $article = $articles[$randomOffset];

        $id = $article->getId();
        $client->loginUser($admin);

        $client->request('DELETE', '/api/articles/'.$id);

        $deltedArticle = $articleRepository->findOneById($id);
        $this->assertSame(null, $deltedArticle);
        $this->assertSame(Response::HTTP_NO_CONTENT, $client->getResponse()->getStatusCode());
    }


}
