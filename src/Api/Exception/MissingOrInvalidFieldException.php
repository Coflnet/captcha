<?php
/**
 * Coflnet (https://coflnet.com)
 *
 */
namespace UserFrosting\Sprinkle\Captcha\Api\Exception;

use UserFrosting\Sprinkle\Captcha\Api\Exception\ApiException;
/**
 * Missing field Exception.
 *
 * Used if a request is missing a required field
 * @author Lukas Simmet (https://coflnet.com/lukas-simmet)
 */
class MissingOrInvalidFieldException extends ApiException
{
	protected $message = 'Your request is missing a field called ';
    protected $httpErrorCode = 400;
    protected $errorSlug = 'invalid_field';
    protected $userMessage = '';
	/**
	 * Name of the missing field
	 * @var string
	 */
	protected $fieldName;

	/**
	 * Sets the name of the missing field
	 * @param string $name name of the field
	 */
	public function setFieldName($name){
		$this->fieldName = $name;
	}

	public function __construct($name){
		$this->setFieldName($name);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDefaultMessage(){
		return $this->message . $this->fieldName;
	}
}
