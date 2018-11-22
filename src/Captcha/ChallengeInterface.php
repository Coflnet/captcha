<?php
/**
 *
 * @copyright Copyright (c) 2018 Ekwav (Coflnet)
 */
namespace UserFrosting\Sprinkle\Captcha\Captcha;

interface ChallengeInterface {

	/**
	* Is expected to return a new challenge
	*/
	public function newChallenge();

	/**
	* Should validate data for a challenge and return a new token on success
	*/
    public function validateChallenge($data);

	/**
	* Should return an unique identifier for this challenge type
	*/
	public function getSlug();

	/**
	 * Should return additional data for a challange
	 * @param  string $identifier Decrypted token
	 * @return [type]             [description]
	 */
	public function getAdditionalData($identifier);
}
