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

        $article = $articles[0];

        $id = $article->getId();
        $articleRepo = $articleRepository->findOneById($id);
        $client->request('GET', '/api/articles/'.$id);
        var_dump(json_decode($client->getResponse(), true));
        die;
       
        $articleApi = json_decode($client->getResponse(), true);

        var_dump($articleApi);
        die;
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
        
        // $articlesApi = json_decode($client->getResponse(), true);
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

        $articlesApi = json_decode($client->getResponse(), true);

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

    /**
     * @group unAuth
     * @group post
     * @group 403
     */
    public function testUnauthCreateArticles(): void 
    {
        $client = static::createClient();
        $jsonData = json_encode([
            'title'=> 'titre',
            'content'=> 'mon article'.random_int(0, 1000),
            'isPublished' => random_int(0, 1)
        ]);

        $client->request('POST', '/api/articles/', [], [], ['CONTENT_TYPE' => 'application/json'], $jsonData);

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
    }

    /**
     * @group authUser
     * @group post
     * @group 403
     */
    public function testAuthUserCreateArticles(): void 
    {

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('user@emile.blog');
        $client->loginUser($user);

        $jsonData = json_encode([
            'title'=> 'titre',
            'content'=> 'mon article'.random_int(0, 1000),
            'isPublished' => random_int(0, 1)
        ]);

        $client->request('POST', '/api/articles/', [], [], ['CONTENT_TYPE' => 'application/json'], $jsonData);

        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
        
    }


    /**
     * @group authAdmin
     * @group post
     * @group 201
     */
    public function testAuthADminCreateArticles(): void 
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $admin = $userRepository->findOneByEmail('admin@emile.blog');
        $client->loginUser($admin);

        $jsonData = json_encode([
            'title'=> 'titre',
            'content'=> 'mon article'.random_int(0, 1000),
            'isPublished' => random_int(0, 1)
        ]);

        $client->request('POST', '/api/articles/', [], [], ['CONTENT_TYPE' => 'application/json'], $jsonData);

        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        
    }

    /**
     * @group unAuth
     * @group put
     * @group 403
     */
    public function testUnauthEditArticles(): void 
    {

        $client = static::createClient();
        $articleRepository = static::getContainer()->get(ArticleRepository::class);
        $articles = $articleRepository->findAll();
        $count = $articleRepository->findCount();
        $index = random_int(0, $count-1);
        $article = $articles[$index];
        $id = $article->getId();

        do {

            $jsonData = json_encode([
                'title'=> $article->getTitle(),
                'content'=> 'Mon article mis à jour'.random_int(0, 1000),
                'isPublished' => $article->getIsPublished()
            ]);

        } while (false);
        // } while ($jsonData['content'] == $article->getContent());

        $client->request('PUT', '/api/articles/'.$id, [], [], ['CONTENT_TYPE' => 'application/json'], $jsonData);
        $unupdatedArticle = $articleRepository->findOneById($id);

        $this->assertSame($unupdatedArticle->getContent(), $article->getContent());
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

    }

    /**
     * @group authUser
     * @group put
     * @group 403
     */
    public function testAuthUserEditArticles(): void 
    {

        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $articleRepository = static::getContainer()->get(ArticleRepository::class);
        $articles = $articleRepository->findAll();
        $count = $articleRepository->findCount();
        $index = random_int(0, $count-1);
        $article = $articles[$index];
        $id = $article->getId();

        $user = $userRepository->findOneByEmail('user@emile.blog');
        $client->loginUser($user);

        do {

            $jsonData = json_encode([
                'title'=> $article->getTitle(),
                'content'=> 'Mon article mis à jour'.random_int(0, 1000),
                'isPublished' => $article->getIsPublished()
            ]);

        } while (false);
        // } while ($jsonData['content'] == $article->getContent());

        $client->request('PUT', '/api/articles/'.$id, [], [], ['CONTENT_TYPE' => 'application/json'], $jsonData);
        $unupdatedArticle = $articleRepository->findOneById($id);

        $this->assertSame($unupdatedArticle->getContent(), $article->getContent());
        $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
        
    }


    /**
     * @group authAdmin
     * @group put
     * @group 201
     */
    public function testAuthADminEditArticles(): void 
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $articleRepository = static::getContainer()->get(ArticleRepository::class);
        $articles = $articleRepository->findAll();
        $count = $articleRepository->findCount();
        $index = random_int(0, $count-1);
        $article = $articles[$index];
        $id = $article->getId();
        $originalArticleContent = $article->getContent();

        $admin = $userRepository->findOneByEmail('admin@emile.blog');
        $client->loginUser($admin);

        do {

            $jsonData = json_encode([
                'title'=> $article->getTitle(),
                'content'=> 'Mon article mis à jour n° '.random_int(0, 1000),
                'isPublished' => $article->getIsPublished()
            ]);

        } while (false);
        // } while ($jsonData['content'] == $article->getContent());

        $client->request('PUT', '/api/articles/'.$id, [], [], ['CONTENT_TYPE' => 'application/json'], $jsonData);
        $updatedArticle = $articleRepository->findOneById($id);

        $this->assertNotSame($updatedArticle->getContent(), $originalArticleContent);
        $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        
    }


}
