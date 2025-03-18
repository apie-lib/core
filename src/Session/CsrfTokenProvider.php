<?php
namespace Apie\Core\Session;

interface CsrfTokenProvider
{
    public function createToken(): string;
    public function validateToken(string $token): void;
}
