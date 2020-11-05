<?php

namespace App\Notification;

use App\Entity\Comment;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackDividerBlock;
use Symfony\Component\Notifier\Bridge\Slack\Block\SlackSectionBlock;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;

class CommentReviewNotification extends Notification implements EmailNotificationInterface, ChatNotificationInterface
{
    /**
     * @var Comment
     */
    private $comment;

    /**
     * CommentReviewNotification constructor.
     *
     * @param Comment $comment Comment posted
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;

        parent::__construct('New comment posted');
    }

    public function asEmailMessage(Recipient $recipient, string $transport = null): ?EmailMessage
    {
        $message = EmailMessage::fromNotification($this, $recipient, $transport);
        $message->getMessage()
            ->htmlTemplate('emails/comments/notification.html.twig')
            ->context(['comment' => $this->comment]);

        return $message;
    }

    public function getChannels(Recipient $recipient): array
    {
        if (preg_match('{\b(great|awesome)\b}i', $this->comment->getText())) {
            return ['email', 'chat/slack'];
        }

        $this->importance(Notification::IMPORTANCE_LOW);

        return ['email'];
    }

    public function asChatMessage(Recipient $recipient, string $transport = null): ?ChatMessage
    {
        if ('slack' !== $transport) {
            return null;
        }

        $message = ChatMessage::fromNotification($this/*, $recipient*/);
        $message->subject($this->getSubject());
        $message->transport($transport);
        $message->options((new SlackOptions())
            ->iconEmoji('tada')
            ->iconUrl('https://guestbook.example.com')
            ->username('Guestbook')
            ->block((new SlackSectionBlock())->text($this->getSubject()))
            ->block(new SlackDividerBlock())
            ->block(
                (new SlackSectionBlock())
                    ->text(sprintf(
                        '%s (%s) says: %s',
                        $this->comment->getAuthor(),
                        $this->comment->getEmail(),
                        $this->comment->getText()
                    ))
            )
        );

        return $message;
    }
}