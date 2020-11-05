<?php

namespace App\Message;

class CommentMessage
{
    /**
     * @var int
     */
    private $commentId;

    /**
     * @var array
     */
    private $context;

    /**
     * @var string
     */
    private $reviewURL;

    public function __construct(int $commentId, string $reviewURL, array $context = [])
    {
        $this->commentId = $commentId;
        $this->context = $context;
        $this->reviewURL = $reviewURL;
    }

    /**
     * @return int
     */
    public function getCommentId(): int
    {
        return $this->commentId;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    public function getReviewUrl(): string
    {
        return $this->reviewURL;
    }
}
