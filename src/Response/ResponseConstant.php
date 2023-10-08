<?php

namespace Primeskills\ApiCommon\Response;

class ResponseConstant
{
    const SUCCESS_CODE = "000";
    const SUCCESS_DESCRIPTION = "Success";
    const NOT_FOUND_CODE = "040";
    const NOT_FOUND_DESCRIPTION = "Not found data";
    const METHOD_NOT_ALLOWED_CODE = "045";
    const METHOD_NOT_ALLOWED_DESCRIPTION = "Method Not Allowed";
    const BAD_REQUEST_CODE = "004";
    const BAD_REQUEST_DESCRIPTION = "Bad Request";
    const UNAUTHORIZED_CODE = "041";
    const UNAUTHORIZED_DESCRIPTION = "Unauthorized";
    const ROLE_FORBIDDEN_ACCESS_CODE = "043";
    const ROLE_FORBIDDEN_ACCESS_DESCRIPTION = "Role user forbidden access";
    const GENERAL_ERROR = "999";
    const GENERAL_ERROR_DESCRIPTION = "Something went wrong";
    const VALIDATION_ERROR_CODE = "022";
    const VALIDATION_ERROR_DESCRIPTION = "Validation form failed";
    const TOKEN_MISMATCH_CODE = "019";
    const TOKEN_MISMATCH_DESCRIPTION = "Token Mismatch";
}
