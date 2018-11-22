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
class CustomMessageApiException extends ApiException
{
	/**
	 * Constructor
	 * @param [type] $errorSlug      Unique identifier for this error
	 * @param [type] $defaultMessage Message for the developer
	 * @param [type] $userMessage    [description]
	 * @param [type] $responseCode   [description]
	 */
	public function __construct($errorSlug,$defaultMessage,$userMessage = null,$responseCode = 400){
		$this->errorSlug = $errorSlug;
		$this->message = $defaultMessage;
		$this->userMessage = $userMessage;
		$this->httpErrorCode = $responseCode;
	}
}
