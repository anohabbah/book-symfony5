<?php

namespace App\Controller;

use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
