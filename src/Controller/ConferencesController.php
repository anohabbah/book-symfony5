<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferencesController extends AbstractController
{
    /**
     * @Route("/", name="home_page")
     *
     * @param Environment          $twig       Twig environment
     * @param ConferenceRepository $repository Conference Repository
     *
     * @return Response The ongoing response
     */
    public function index(Environment $twig, ConferenceRepository $repository): Response
    {
        return new Response($twig->render('conferences/index.html.twig', [
            'conferences' => $repository->findAll(),
        ]));
    }

    /**
     * @Route("/conferences/{id}", name="conference.show")
     *
     * @param Request           $request           Request
     * @param Environment       $twig              Twig environment
     * @param Conference        $conference        Conference to show
     * @param CommentRepository $commentRepository Comment repository
     *
     * @return Response Outgoing response
     */
    public function show(
        Request $request,
        Environment $twig,
        Conference $conference,
        CommentRepository $commentRepository
    ): Response {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response($twig->render('conferences/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::ITEMS_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::ITEMS_PER_PAGE),
        ]));
    }
}
