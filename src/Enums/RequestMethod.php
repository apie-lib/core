<?php
namespace Apie\Core\Enums;

enum RequestMethod: string
{
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case PATCH = 'PATCH';
    case GET = 'GET';
    case HEAD = 'HEAD';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
    case PROPFIND = 'PROPFIND';
    case PROPPATCH = 'PROPPATCH';
    case MKCOL = 'MKCOL';
    case COPY = 'COPY';
    case MOVE = 'MOVE';
    case LOCK = 'LOCK';
    case UNLOCK = 'UNLOCK';
    case ANY = 'ANY';

    /**
     * @return string|array<string>
     */
    public function toSymfonyRequestMethod(): string|array
    {
        if ($this === self::ANY) {
            return [];
        }
        return $this->value;
    }

    /**
     * @return array<RequestMethod>
     */
    public static function allowedInOpenApi(): array
    {
        return [
            self::POST,
            self::PUT,
            self::DELETE,
            self::PATCH,
            self::GET,
            self::HEAD,
            self::OPTIONS,
            self::TRACE,
        ];
    }
}
