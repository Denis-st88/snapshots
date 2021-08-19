<?php

declare(strict_types=1);

namespace App\Auth\Test\Entity\User;

use Ramsey\Uuid\Uuid;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use App\Auth\Entity\User\Token;
use PHPUnit\Framework\InvalidArgumentException;

/**
 * @covers Token
 */
class TokenTest extends TestCase
{
    public function testSuccess(): void
    {
        $token = new Token(
            $value = Uuid::uuid4()->toString(),
            $expires = new DateTimeImmutable()
        );

        self::assertEquals($value, $token->getValue());
        self::assertEquals($expires, $token->getExpires());
    }

    public function testCase(): void
    {
        $value = Uuid::uuid4()->toString();
        $token = new Token(mb_strtoupper($value), new DateTimeImmutable());

        self::assertEquals($value, $token->getValue());
    }

    public function testIncorrect(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('12345', new DateTimeImmutable());
    }

    public function testEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Token('', new DateTimeImmutable());
    }
}