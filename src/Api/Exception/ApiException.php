<?php
/**
 * Coflnet (https://coflnet.com)
 *
 */
namespace UserFrosting\Sprinkle\Captcha\Api\Exception;

use UserFrosting\Support\Exception\HttpException;
/**
 * General Api Exception.
 *
 * @author Lukas Simmet (https://coflnet.com/lukas-simmet)
 */
class ApiException extends HttpException
{
	/**
	 * Unique identifier for this error
	 * @var string
	 */
	protected $errorSlug = 'api_error';
	/**
	 * Http status error code
	 * @var int
	 */
    protected $httpErrorCode = 400;
	/**
	 * A short but helpful human readable message for a developer
	 * @var string
	 */
	protected $message = 'Something went wrong, we are not sure what';
	/**
	 * A short message for the enduser to tell him what went wrong or what to do now
	 * @var string
	 */
	protected $userMessage = '';
	/**
	 * Optional link for the developer with more information/docuentation
	 * @var string
	 */
	protected $link = null;

	public function getDefaultMessage(){
		return $this->message;
	}

	public function getHttpErrorCode(){
		return $this->httpErrorCode;
	}

	public function getErrorSlug(){
		return $this->errorSlug;
	}

	public function getUserMessage(){
		return $this->userMessage;
	}

	public function setMessage($message){
		$this->message = $message;
	}

	public function setUserMessage($userMessage){
		$this->userMessage = $userMessage;
	}

	public function setLink($link){
		$this->link = $link;
	}

	public function getLink(){
		return $this->link;
	}
}
