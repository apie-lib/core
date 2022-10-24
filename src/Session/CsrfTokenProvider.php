<?php
namespace Apie\Core\Session;

interface CsrfTokenProvider
{
    public function createToken(): string;
}