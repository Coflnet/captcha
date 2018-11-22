<?php
/**
 * Coflnet (https://coflnet.com)
 *
 */
namespace UserFrosting\Sprinkle\Captcha\Api\Exception;

use UserFrosting\Sprinkle\Captcha\Api\Exception\ApiException;
/**
 * Custom Api Exception.
 *
 * implements a construt whih takes error data
 * @author Äkwav (https://coflnet.com/ekwav)
 */
class InvalidTokenException extends ApiException
{
	protected $message = 'One of your tokens is invalid, please request a new challenge';
    protected $httpErrorCode = 400;
    protected $errorSlug = 'token_invalid';
    protected $userMessage = 'An error occured, please try again.';
}
