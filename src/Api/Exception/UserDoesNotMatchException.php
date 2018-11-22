<?php
/**
 * Coflnet (https://coflnet.com)
 *
 */
namespace UserFrosting\Sprinkle\Captcha\Api\Exception;

use UserFrosting\Sprinkle\Captcha\Api\Exception\ApiException;
/**
 * User does not match Exception.
 *
 * thrown when a token should be validated but the userId in the token and the given userId don't match
 * @author Äkwav (https://coflnet.com/ekwav)
 */
class UserDoesNotMatchException extends ApiException
{
	protected $message = 'The Id for this user and the user Id for this token don\'t match';
    protected $httpErrorCode = 400;
    protected $errorSlug = 'invalid_token';
    protected $userMessage = 'An error occured, please try again.';
}
