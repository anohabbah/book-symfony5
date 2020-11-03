<?php

namespace App\Tests;

use App\Entity\Comment;
use App\SpamChecker;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SpamCheckerTest extends TestCase
{
    /** @test */
    public function spam_score_with_invalid_request(): void
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();
        $context = [];

        $client = new MockHttpClient(new MockResponse('invalid', [
            'response_headers' => ['x-akismet-debug-help: Invalid key'],
        ]));
        $checker = new SpamChecker($client, 'abcd');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to check for spam: invalid (Invalid key).');

        $checker->getSpamScore($comment, $context);
    }

    /**
     * @test
     * @dataProvider getComments
     *
     * @param int               $expectedScore Expected score
     * @param ResponseInterface $response      Mocked response
     * @param Comment           $comment       A comment model
     * @param array             $context       The context
     */
    public function it_test_spam_score(int $expectedScore, ResponseInterface $response, Comment $comment, array $context): void
    {
        $client = new MockHttpClient([$response]);
        $checker = new SpamChecker($client, 'abcde');

        $score = $checker->getSpamScore($comment, $context);
        $this->assertSame($expectedScore, $score);
    }

    public function getComments(): iterable
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();
        $context = [];

        $response = new MockResponse('', ['response_headers' => ['x-akismet-pro-tip: discard']]);
        yield 'blatant_spam' => [2, $response, $comment, $context];

        $response = new MockResponse('true');
        yield 'spam' => [1, $response, $comment, $context];

        $response = new MockResponse('false');
        yield 'ham' => [0, $response, $comment, $context];
    }
}
