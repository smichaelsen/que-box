<?php
namespace App\Http;

use Symfony\Component\HttpFoundation\Response;

class NoContentResponse extends Response
{
    public function __construct(array $headers = [])
    {
        parent::__construct('', 204, $headers);
    }
}