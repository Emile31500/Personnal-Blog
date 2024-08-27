<?php

namespace Tests\Controller;

use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ProjectControllerTest extends WebTestCase {


        /**
         * @group unAuth
         * @group get
         * @group 403
         * */
        public function testGetUnpublishedProjectsUnauth() : void
        {
            $client = static::createClient();

            $client->request('GET', '/api/unpublished/projects/');

            $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

        }  
    
        /**
         * @group autUser
         * @group get
         * @group 403
         * */
        public function testGetUnpublishedProjectsAuthUser() : void
        {
            $client = static::createClient();

            $userRepository = static::getContainer()->get(UserRepository::class);
            $user = $userRepository->findOneByEmail('user@emile.blog');
            $client->loginUser($user);
            $client->request('GET', '/api/unpublished/projects/');

            $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

        }

        /**
         * @group authAdmin
         * @group get
         * @group 200
         * */
        public function testGetUnpublishedProjectsAuthAdmin() : void
        {
            $client = static::createClient();

            $userRepository = static::getContainer()->get(UserRepository::class);
            $user = $userRepository->findOneByEmail('admin@emile.blog');
            $client->loginUser($user);
            $client->request('GET', '/api/unpublished/projects/');
        
            $projectsApi = json_decode($client->getResponse()->getContent(), true);

            $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());

            foreach ($projectsApi as $projectApi) {

                $this->assertIsInt($projectApi['id']);
                $this->assertFalse($projectApi['isPublished']);

            }
        }

        /**
         * @group unAuth
         * @group get
         * @group 200
         * */
        public function testGetProjectDetailUnAuth() : void
        {
            $client = static::createClient();
            $projectRepository = static::getContainer()->get(ProjectRepository::class);
            $projects = $projectRepository->findByPublishedProject(0);

            $project = $projects[0];

            $id = $project->getId();
            $projectRepo = $projectRepository->findOneById($id);
            $client->request('GET', '/api/projects/'.$id);
        
            $projectApi = json_decode($client->getResponse()->getContent(), true);

            $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
            $this->assertSame($projectRepo->getTitle(), $projectApi['title']);
            $this->assertTrue($projectApi['isPublished']);

        }


         /**
         * @group unAuth
         * @group post
         * @group 403
         * */
        public function testCreateProjectDetailUnAuth() : void
        {

            $client = static::createClient();    

            $num = random_int(0, 1000);
            $jsonData = json_encode([
                'title' => 'Titre du projet '.$num, 
                'githubLink' => 'https://github.com/Emile31500/Project-'.$num, 
                'content' => 'Voici le contenu du projet numéro n° '.$num , 
                'isPublished' =>(1 ==random_int(0, 1))
            ], true);
        
            $client->request('POST', '/api/projects/'.$id, [], [], ['CONTENT_TYPE' => 'application/json'], $jsonData);

            $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());
        }

        
         /**
         * @group authUser
         * @group post
         * @group 403
         * */
        public function testCreateProjectDetailAuthUser() : void
        {
            $client = static::createClient();    

            $num = random_int(0, 1000);
            $jsonData = json_encode([
                'title' => 'Titre du projet '.$num, 
                'githubLink' => 'https://github.com/Emile31500/Project-'.$num, 
                'content' => 'Voici le contenu du projet numéro n° '.$num , 
                'isPublished' =>(1 ==random_int(0, 1))
            ], true);

            $userRepository = static::getContainer()->get(UserRepository::class);
            $user = $userRepository->findOneByEmail('user@emile.blog');
            $client->loginUser($user);

            $client->request('POST', '/api/projects/'.$id, [], [], ['CONTENT_TYPE' => 'application/json'], $jsonData);

            $this->assertSame(Response::HTTP_FORBIDDEN, $client->getResponse()->getStatusCode());

        }

        
         /**
         * @group authAdmin
         * @group post
         * @group 201
         * */
        public function testCreateProjectDetailAuthAdmin() : void
        {
            $client = static::createClient();    

            $num = random_int(0, 1000);
            $jsonData = json_encode([
                'title' => 'Titre du projet '.$num, 
                'githubLink' => 'https://github.com/Emile31500/Project-'.$num, 
                'content' => 'Voici le contenu du projet numéro n° '.$num , 
                'isPublished' =>(1 ==random_int(0, 1))
            ], true);
        
            $userRepository = static::getContainer()->get(UserRepository::class);
            $user = $userRepository->findOneByEmail('admin@emile.blog');
            $client->loginUser($user);

            $client->request('POST', '/api/projects/'.$id, [], [], ['CONTENT_TYPE' => 'application/json'], $jsonData);

            $projectApi = json_decode($client->getResponse()->getContent(), true);

            $this->assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
            $this->assertSame($projectApi['title'], 'Titre du projet '.$num);
            $this->assertIsInt($projectApi['id']);

        }

        /**
         * @group unAuth
         * @group delete
         * @group 403
         * */
        public function testDeleteProjectDetailUnAuth() : void
        {

        }

        
         /**
         * @group authUser
         * @group delete
         * @group 403
         * */
        public function testDeleteProjectDetailAuthUser() : void
        {

        }

        
         /**
         * @group authAdmin
         * @group delete
         * @group 204
         * */
        public function testDeleteProjectDetailAuthAdmin() : void
        {

        }

        /**
         * @group unAuth
         * @group put
         * @group 403
         * */
        public function testUpdateProjectDetailUnAuth() : void
        {

        }

        
         /**
         * @group authUser
         * @group put
         * @group 403
         * */
        public function testUpdateProjectDetailAuthUser() : void
        {

        }

        
         /**
         * @group authAdmin
         * @group put
         * @group 201
         * */
        public function testUpdateProjectDetailAuthAdmin() : void
        {

        }


}
