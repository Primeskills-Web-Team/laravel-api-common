<?php

namespace Primeskills\ApiCommon\Response;
class Response
{
    public static function builder(): ResponseBuilder
    {
        return new ResponseBuilder();
    }
}
