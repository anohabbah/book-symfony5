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

    public function __construct(int $commentId, array $context = [])
    {
        $this->commentId = $commentId;
        $this->context = $context;
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
}
