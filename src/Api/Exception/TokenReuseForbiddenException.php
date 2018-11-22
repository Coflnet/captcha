<?php
/**
 * Coflnet (https://coflnet.com)
 *
 */
namespace UserFrosting\Sprinkle\Captcha\Api\Exception;

use UserFrosting\Sprinkle\Captcha\Api\Exception\ApiException;
/**
 * Token reuse forbidden Exception.
 *
 * Used for when a token is used again after vertification
 * @author Lukas Simmet (https://coflnet.com/lukas-simmet)
 */
class TokenReuseForbiddenException extends ApiException
{
	protected $message = 'This token has already been used before, it is not allowed to use tokens twice';
    protected $httpErrorCode = 400;
    protected $errorSlug = 'token_reuse_forbidden';


}
