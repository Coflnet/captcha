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
class NotEnoughImageTargetsException extends ApiException
{
	protected $message = 'There aren\'t enough folders with images under the app/captcha/images folder. Add new folders with the label of the images in that folder (and some images) to that folder.';
    protected $errorSlug = 'installation_configured_wrong';
}
