<?php

namespace App\Controller;

use App\Entity\Project;
use Doctrine\ORM\EntityManager;
use App\Repository\ProjectRepository;
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

class ProjectController extends AbstractController
{
    #[Route('/projet/page/{index_page}',  name: 'app_project_list',)]
    public function projectList(int $index_page, ProjectRepository $projectRepository): Response
    {
        
        $projects = $projectRepository->findPublishedProject($index_page-1);

        return $this->render('project/list.html.twig', [
            'controller_name' => 'Projets',
            'projects' => $projects
        ]);
    }

    #[Route('/projet/{id}/detail',  name: 'app_project_detail')]
    public function projectDetail(Project $project): Response
    {
        return $this->render('project/detail.html.twig', [
            'project' => $project
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/projet/{id_project}/edition',  name: 'app_project_edit',)]
    public function projectEdition(): Response
    {
        return $this->render('project/edit.html.twig', [
            'controller_name' => 'Edition',
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/projet/create',  name: 'app_project_create',)]
    public function projectCreate(): Response
    {
        return $this->render('project/create.html.twig', [
            'controller_name' => 'Nouveu projet',
        ]);
    }

    #[Route('/api/projects/{id}', methods: 'GET',  name: 'api_project_detail')]
    public function getDetailProject(Project $project, Security $security, SerializerInterface $serializer): Response
    {
        if ($security->isGranted('ROLE_ADMIN') || $project->isPublished()) {

            $jsonProjects = $serializer->serialize($project, 'json', ['groups' => 'project']);
            return new Response($jsonProjects, Response::HTTP_OK, ['Content-Type' => 'application/json']);

        }

        return new Response('', Response::HTTP_NOT_FOUND, ['Content-Type' => 'application/json']);
    }

    #[Route('/api/unpublished/projects/', methods: 'GET',  name: 'api_project_unpublished',)]
    public function getUnpublishedProject(ProjectRepository $projectRepository, Security $security, SerializerInterface $serializer): Response
    {

        if (!$security->isGranted('ROLE_ADMIN')) {

            return new Response('', Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);
            
        } else {

            $projects = $projectRepository->findByUnpublishedProject();
            $jsonProjects = $serializer->serialize($projects, 'json', ['groups' => 'project']);

            return new Response($jsonProjects, Response::HTTP_OK, ['Content-Type' => 'application/json']);

        }

    }

    #[Route('/api/projects/{id}', methods: 'DELETE',  name: 'api_delete_project',)]
    public function deleteProject(Project $project, EntityManagerInterface $em, Security $security): Response
    {

        if (!$security->isGranted('ROLE_ADMIN')) {

            return new Response('', Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);
            
        } else {

            $em->remove($project);
            $em->flush();

            return new Response('', Response::HTTP_NO_CONTENT, ['Content-Type' => 'application/json']);

        }

    }

    #[Route('/api/projects/', methods: 'POST',  name: 'api_project_create',)]
    public function postProject(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator, Security $security): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {

            $project = $serializer->deserialize($request->getContent(), Project::class, 'json');
            $errors = $validator->validate($project);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                return new Response($errorsString, Response::HTTP_BAD_REQUEST);
            }
            
            $em->persist($project);
            $em->flush();
    
            $jsonProject = $serializer->serialize($project, 'json', ['groups' => 'project']);
            return new Response($jsonProject, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);

        } else {

            return new Response('', Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);

        }
    }

    #[Route('/api/projects/{id}', methods: 'PUT',  name: 'api_project_edit',)]
    public function editProject(Project $project, Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator, Security $security): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {

            $project = $serializer->deserialize($request->getContent(), Project::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $project]);
            $errors = $validator->validate($project);

            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                return new Response($errorsString, Response::HTTP_BAD_REQUEST);
            }
            
            $em->persist($project);
            $em->flush();
    
            $jsonProject = $serializer->serialize($project, 'json');
            return new Response($jsonProject, Response::HTTP_CREATED, ['Content-Type' => 'application/json']);

        } else {

            return new Response('', Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json']);

        }
    }
}
