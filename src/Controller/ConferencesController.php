<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Conference;
use App\Form\CommentFormType;
use App\Message\CommentMessage;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(Environment $twig, EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->twig = $twig;
        $this->entityManager = $entityManager;
        $this->bus = $bus;
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
        $response = new Response($this->twig->render('conferences/index.html.twig', [
            'conferences' => $repository->findAll(),
        ]));
        $response->setSharedMaxAge(3600);

        return $response;
    }

    /**
     * @Route("/conference_header", name="conferences.header")
     *
     * @param ConferenceRepository $conferenceRepository Conference repository
     */
    public function conferenceHeader(ConferenceRepository $conferenceRepository): Response
    {
        $response = new Response($this->twig->render('conferences/header.html.twig',
            [
                'conferences' => $conferenceRepository->findAll(),
            ])
        );
        $response->setSharedMaxAge(3600);

        return $response;
    }

    /**
     * @Route("/conferences/{slug}", name="conference.show")
     *
     * @param Request           $request           Request
     * @param Conference        $conference        Conference to show
     * @param CommentRepository $commentRepository Comment repository
     * @param NotifierInterface $notifier          Message notifier
     * @param string            $photoDir          Directory where to upload files
     *
     * @return Response Outgoing response
     */
    public function show(
        Request $request,
        Conference $conference,
        CommentRepository $commentRepository,
        NotifierInterface $notifier,
        string $photoDir
    ): Response {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setConference($conference);

            if ($photo = $form['photo']->getData()) {
                try {
                    $filename = bin2hex(random_bytes(6)).'.'.$photo->getExtension();
                    $photo->move($photoDir, $filename);
                    $comment->setPhotoFilename($filename);
                } catch (\Exception $e) {
                    // unable to upload the photo, give up
                }
            }

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            $context = [
                'user_ip' => $request->getClientIp(),
                'user_agent' => $request->headers->get('user-agent'),
                'referrer' => $request->headers->get('referrer'),
                'permalink' => $request->getUri(),
            ];

            $reviewURL = $this->generateUrl(
                'comments.review',
                ['id' => $comment->getId()],
                UrlGeneratorInterface::ABSOLUTE_PATH
            );
            $this->bus->dispatch(new CommentMessage($comment->getId(), $reviewURL, $context));

            $notifier->send(new Notification(
                'Thank you  for your feedback. Your comment will be posted after moderation',
                ['browser']
            ));

            return $this->redirectToRoute('conference.show', ['slug' => $conference->getSlug()]);
        }

        if ($form->isSubmitted()) {
            $notifier->send(new Notification(
                'Can you check your submission ? There are some problems with it.',
                ['browser']
            ));
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
