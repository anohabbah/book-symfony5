<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Message\CommentMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

class AdminController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var MessageBusInterface
     */
    private $bus;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $bus)
    {
        $this->entityManager = $entityManager;
        $this->bus = $bus;
    }

    /**
     * @Route("/admin/comments/review/{id}", name="comments.review")
     *
     * @param Request  $request  Incoming request
     * @param Comment  $comment  Comment to review
     * @param Registry $registry Workflow registry
     *
     * @return Response Outgoing response
     */
    public function reviewComment(Request $request, Comment $comment, Registry $registry): Response
    {
        $accepted = !$request->query->get('reject');

        $machine = $registry->get($comment);
        if ($machine->can($comment, 'publish')) {
            $transition = $accepted ? 'publish' : 'reject';
        } elseif ($machine->can($comment, 'publish_ham')) {
            $transition = $accepted ? 'publish_ham' : 'reject_ham';
        } else {
            return new Response('Comment already reviewed or not in the right state.');
        }

        $machine->apply($comment, $transition);
        $this->entityManager->flush();

        if ($accepted) {
            $this->bus->dispatch(new CommentMessage($comment->getId()));
        }

        return $this->render('admin/review.html.twig', [
            'transition' => $transition,
            'comment' => $comment,
        ]);
    }

    /**
     * @Route("/admin/http-cache/{uri<.*>}", methods={"PURGE"})
     *
     * @param KernelInterface $kernel  Http kernel
     * @param Request         $request Incoming request
     * @param string          $uri     URI
     */
    public function purgeHttpCache(KernelInterface $kernel, Request $request, string $uri): Response
    {
        if ('prod' === $kernel->getEnvironment()) {
            return new Response('KO', 400);
        }

        $store = (new class($kernel) extends HttpCache {
        })->getStore();
        $store->purge($request->getSchemeAndHttpHost().'/'.$uri);

        return new Response('Done');
    }
}
