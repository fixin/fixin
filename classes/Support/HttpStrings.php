<?php
/**
 * Fixin Framework
 *
 * Copyright (c) Attila Jenei
 *
 * http://www.fixinphp.com
 */

namespace Fixin\Support;

class HttpStrings extends DoNotCreate
{
    public const
        STATUSES = [
            Http::STATUS_CONTINUE_100 => 'Continue',
            Http::STATUS_SWITCHING_PROTOCOLS_101 => 'Switching protocols',
            Http::STATUS_PROCESSING_102 => 'Processing',
            Http::STATUS_OK_200 => 'Ok',
            Http::STATUS_CREATED_201 => 'Created',
            Http::STATUS_ACCEPTED_202 => 'Accpeted',
            Http::STATUS_NON_AUTHORITATIVE_INFORMATION_203 => 'Non-authoritative information',
            Http::STATUS_NO_CONTENT_204 => 'No content',
            Http::STATUS_RESET_CONTENT_205 => 'Reset content',
            Http::STATUS_PARTIAL_CONTENT_206 => 'Partial content',
            Http::STATUS_MULTI_STATUS_207 => 'Multi status',
            Http::STATUS_ALREADY_REPORTED_208 => 'Already reported',
            Http::STATUS_MULTIPLE_CHOICES_300 => 'Multiple choices',
            Http::STATUS_MOVED_PERMANENTLY_301 => 'Moved permanently',
            Http::STATUS_FOUND_302 => 'Found',
            Http::STATUS_SEE_OTHER_303 => 'See other',
            Http::STATUS_NOT_MODIFIED_304 => 'Not modified',
            Http::STATUS_USE_PROXY_305 => 'Use proxy',
            Http::STATUS_SWITCH_PROXY_306 => 'Switch proxy',
            Http::STATUS_TEMPORARY_REDIRECT_307 => 'Temporary redirect',
            Http::STATUS_BAD_REQUEST_400 => 'Bad request',
            Http::STATUS_UNAUTHORIZED_401 => 'Unauthorized',
            Http::STATUS_PAYMENT_REQUIRED_402 => 'Payment required',
            Http::STATUS_FORBIDDEN_403 => 'Forbidden',
            Http::STATUS_NOT_FOUND_404 => 'Not found',
            Http::STATUS_METHOD_NOT_ALLOWED_405 => 'Method not allowed',
            Http::STATUS_NOT_ACCEPTABLE_406 => 'Not acceptable',
            Http::STATUS_PROXY_AUTHENTICATION_REQUIRED_407 => 'Proxy authentication required',
            Http::STATUS_REQUEST_TIME_OUT_408 => 'Request time out',
            Http::STATUS_CONFLICT_409 => 'Conflict',
            Http::STATUS_GONE_410 => 'Gone',
            Http::STATUS_LENGTH_REQUIRED_411 => 'Length required',
            Http::STATUS_PRECONDITION_FAILED_412 => 'Precondition failed',
            Http::STATUS_REQUEST_ENTITY_TOO_LARGE_413 => 'Request entity too large',
            Http::STATUS_REQUEST_URI_TOO_LONG_414 => 'Request URI too long',
            Http::STATUS_UNSUPPORTED_MEDIA_TYPE_415 => 'Unsupported media type',
            Http::STATUS_REQUESTED_RANGE_NOT_SATISFIABLE_416 => 'Request range not satisfiable',
            Http::STATUS_EXPECTATION_FAILED_417 => 'Expectation failed',
            Http::STATUS_I_M_A_TEAPOT_418 => "I'm a teapot",
            Http::STATUS_UNPROCESSABLE_ENTITY_422 => 'Unprocessable entity',
            Http::STATUS_LOCKED_423 => 'Locked',
            Http::STATUS_FAILED_DEPENDENCY_424 => 'Failed dependency',
            Http::STATUS_UNORDERED_COLLECTION_425 => 'Unordered collection',
            Http::STATUS_UPGRADE_REQUIRED_426 => 'Upgrade required',
            Http::STATUS_PRECONDITION_REQUIRED_428 => 'Precondition required',
            Http::STATUS_TOO_MANY_REQUESTS_429 => 'Too many requests',
            Http::STATUS_REQUEST_HEADER_FIELDS_TOO_LARGE_431 => 'Request header fields too large',
            Http::STATUS_INTERNAL_SERVER_ERROR_500 => 'Internal server error',
            Http::STATUS_NOT_IMPLEMENTED_501 => 'Not implemented',
            Http::STATUS_BAD_GATEWAY_502 => 'Bad gateway',
            Http::STATUS_SERVICE_UNAVAILABLE_503 => 'Service unavailable',
            Http::STATUS_GATEWAY_TIME_OUT_504 => 'Gateway time out',
            Http::STATUS_HTTP_VERSION_NOT_SUPPORTED_505 => 'HTTP version not supported',
            Http::STATUS_VARIANT_ALSO_NEGOTIATES_506 => 'Variant also negotiates',
            Http::STATUS_INSUFFICIENT_STORAGE_507 => 'Insufficient storage',
            Http::STATUS_LOOP_DETECTED_508 => 'Loop detected',
            Http::STATUS_NETWORK_AUTHENTICATION_REQUIRED_511 => 'Network authentication required',
    ];
}
