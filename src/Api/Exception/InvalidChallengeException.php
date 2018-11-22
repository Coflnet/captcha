<?php
/**
 * Coflnet (https://coflnet.com)
 *
 */
namespace UserFrosting\Sprinkle\Captcha\Api\Exception;

use UserFrosting\Sprinkle\Captcha\Api\Exception\ApiException;
/**
 * Invalid challenge Exception.
 *
 * Thrown when a non existing challenge type was requested
 * @author Lukas Simmet (https://coflnet.com/lukas-simmet)
 */
class InvalidChallengeException extends ApiException
{
	protected $message = 'The requested challenge doesn\'t exist.';
    protected $httpErrorCode = 400;
    protected $errorSlug = 'invalid_challenge';


}
