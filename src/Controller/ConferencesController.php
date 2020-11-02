<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ConferencesController extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/", name="home_page")
     *
     * @param ConferenceRepository $repository Conference Repository
     *
     * @return Response The ongoing response
     */
    public function index(ConferenceRepository $repository): Response
    {
        return new Response($this->twig->render('conferences/index.html.twig', [
            'conferences' => $repository->findAll(),
        ]));
    }

    /**
     * @Route("/conferences/{slug}", name="conference.show")
     *
     * @param Request           $request           Request
     * @param Conference        $conference        Conference to show
     * @param CommentRepository $commentRepository Comment repository
     *
     * @return Response Outgoing response
     */
    public function show(
        Request $request,
        Conference $conference,
        CommentRepository $commentRepository
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('conference.show', ['slug' => $conference->getSlug()]);
        }

        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return new Response($this->twig->render('conferences/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::ITEMS_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::ITEMS_PER_PAGE),
            'comment_form' => $form->createView(),
        ]));
    }
}
