<?php
namespace Apie\Core\Session;

use Apie\Core\Exceptions\InvalidCsrfTokenException;

class FakeTokenProvider implements CsrfTokenProvider
{
    public function createToken(): string
    {
        return 'string';
    }
    public function validateToken(string $token): void
    {
        if ($token !== 'string') {
            throw new InvalidCsrfTokenException();
        }
    }
}
