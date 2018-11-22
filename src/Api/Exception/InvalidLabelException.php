<?php
/**
 * Coflnet (https://coflnet.com)
 *
 */
namespace UserFrosting\Sprinkle\Captcha\Api\Exception;

use UserFrosting\Sprinkle\Captcha\Api\Exception\ApiException;
/**
 * Invalid Label Exception.
 *
 * Thrown if too many labels were wrong in a challenge
 * @author Lukas Simmet (https://coflnet.com/lukas-simmet)
 */
class InvalidLabelException extends ApiException
{
	protected $message = 'There were to many wrong labels in the captcha';
    protected $httpErrorCode = 400;
    protected $errorSlug = 'wrong_label';
    protected $userMessage = 'To many wrong images selected, please try again.';


}
