<?php

namespace Test\Controller;

use App\Entity\Article;
use PHPUnit\Framework\TestCase;
use App\Repository\ArticleRepository;
use PHPUnit\Framework\Attibutes\Test;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

Class ArticleControllerTest extends WebTestCase {

    #[Group(API)]
    #[Group(Get)]
    public function testGetArticleDetail()
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

}

;