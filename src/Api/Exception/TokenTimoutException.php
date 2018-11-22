<?php
/**
 * Coflnet (https://coflnet.com)
 *
 */
namespace UserFrosting\Sprinkle\Captcha\Api\Exception;

use UserFrosting\Sprinkle\Captcha\Api\Exception\ApiException;
/**
 * Token Timout Exception.
 *
 * Used for when a token has exeeded its timeout
 * @author Lukas Simmet (https://coflnet.com/lukas-simmet)
 */
class TokenTimoutException extends ApiException
{
	protected $message = 'This token timed out';
    protected $httpErrorCode = 408;
    protected $errorSlug = 'token_timeout';
    protected $userMessage = 'Your request timeout, please try again.';


}
