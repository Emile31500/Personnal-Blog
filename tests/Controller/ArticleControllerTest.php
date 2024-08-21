<?php

namespace Tests\Controller;

use App\Entity\Article;
use PHPUnit\Framework\TestCase;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use PHPUnit\Framework\Attibutes\Test;
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

        $this->assertSame(200, $client->getResponse()->getStatusCode());
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
        $client->request('GET', '/api/articles/unpublished/');
       
        // $articlesApi = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(403, $client->getResponse()->getStatusCode());

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
        $this->assertSame(403, $client->getResponse()->getStatusCode());

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

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        foreach ($articlesApi as $article) {

            $this->assertSame(false, $articlesApi[0]['isPublished']);

        }
    }
}
