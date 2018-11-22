<?php
/**
*
* @copyright Copyright (c) 2018 Ekwav (Coflnet)
*/
namespace UserFrosting\Sprinkle\Captcha\Captcha;

use UserFrosting\Sprinkle\Captcha\Captcha\ChallengeInterface;
use UserFrosting\Sprinkle\Captcha\Api\Exception\TokenTimoutException;
use UserFrosting\Sprinkle\Captcha\Api\Exception\InvalidTokenException;
use UserFrosting\Sprinkle\Captcha\Api\Exception\InvalidChallengeException;
use UserFrosting\Sprinkle\Captcha\Api\Exception\InvalidPassTokenException;

class Captcha {

	/**
	* Array of captcha implementaions
	*/
	protected $challenges = array();

	/**
	 * Userfrosting service container
	 */
	protected $ci;


	public function __construct($ci, ChallengeInterface $challenges = null){
		$this->ci = $ci;
		if($challenges != null)
			$this->addChallange($challenges);
	}

	public function addChallange(ChallengeInterface $challenge) {
		$challengeSlug = $challenge->getSlug();
		if(is_null($this->challenges)){
			$this->challenges = array($challengeSlug=>$challenge);
		} else {
			$this->challenges[$challengeSlug] = $challenge;
		}
	}

	/**
	 * Returns additional data for a given challenge by a token
	 * @param  string $challengeSlug Slug of the challenge
	 * @param  string $token         Token for this data
	 * @return [type]                Some data
	 */
	public function getAdditionalData($challengeSlug,$token){
		return $this->getChallangeBySlug($challengeSlug)->getAdditionalData($this->getTokenData($token));
	}

	protected function getChallangeBySlug($challengeSlug){
		$challenge = $this->challenges[$challengeSlug];
		if (is_null($challenge)){
			throw new InvalidChallengeException();
		}
		return $challenge;
	}

	/**
	* Selects one, at random out of the registed captchas and reqeusts a challenge from it
	*
	* @return object
	*/
	public function getChallenge($userId = null){
		if(!$this->ci->botProbabilityCalculator->isCaptchaRequired($userId)){
			return $this->getPassTokenResponse($userId);
		}
		// select a challenge
		$index = $this->getRandomArrIndex($this->challenges);
		// create response
		$response = new \stdClass();
		$response->slug = $this->challenges[$index]->getSlug();
		$response->challenge = $this->challenges[$index]->newChallenge();
		return $response;
	}

	/**
	 * Returns the userfrosting service container
	 */
	public function getCi(){
		return $this->ci;
	}

	/**
	 * Generates and returns a valid array index
	 * @param  array $array The array which to get an index from
	 * @return int|string       A serure random index
	 */
	public function getRandomArrIndex($array){
		$size = count($array);
		if($size === 0){
			throw new \Exception("Array has to contain more than 0 items to get an index from");
		}
		// to support assciative arrays
		return array_keys($array)[random_int(0,$size-1)];
	}




	/**
	 * Decrypts, json decodes, and validates a given token
	 * @param  string $fileToken The token which to extract the data from
	 * @return object            Data object which went into the token
	 */
	function getTokenData($fileToken) {
		if(strpos($fileToken,'%')!== false ){
			$fileToken = urldecode($fileToken);
		}
		$data = json_decode($this->decrypt($fileToken));
		if(is_null($data)){
			throw new InvalidTokenException();
		}
		if ($data->expireTime < time()) {
			throw new TokenTimoutException();
		}
		if(!$this->ci->cache->has($data->tokenId)){
			throw new InvalidTokenException();
		}
		return $data;
	}

	/**
	 * Invokes a token
	 * Should be called after a challenge is validated to prevent replay attacks
	 * @param  string $tokenId The token which to invoke
	 */
	public function invokeToken($tokenId){
		$this->ci->cache->forget($tokenId);
	}

	/**
	 * Validates a json object with a pass_token and returns an access granted object on success
	 * @param  object $data The data to validate
	 * @return object       JSON encode ready response
	 */
	public function validateAccessToken($data) {
		if(!$this->isPassTokenValid($data->token,$data->userId)){
			return;
		}

		$output = new \stdClass();
		$output->access_granted = true;
		echo json_encode($output);
	}

	/**
	 * Validates a pass token, will throw Exceptions on error
	 * @param  string  $token  The token which to validate
	 * @param  string  $userId Optional UserId which to validate the token with
	 * @return boolean         Wherether or not the token is valid
	 * 						   actually allways true otherwise an exception will be thrown
	 */
	public function isPassTokenValid($token,$userId = null){
		$tokenData = $this->getTokenData($token);

		// if the token is bound to an user test if it matches with the one from the server
		if (!is_null($tokenData->userId) && $tokenData->userId != $userId) {
			throw new UserDoesNotMatchException();
		}
		if($tokenData->validationType !== 'captcha'){
			throw new InvalidPassTokenException();
		}
		if(!$tokenData->access){
			throw new InvalidPassTokenException();
		}

		// this token is now used
		$this->invokeToken($tokenData->tokenId);

		return true;
	}

	/**
	 * Tries to validate a given challenge and returns a pass_token or a new challenge
	 * @param  string $challengeSlug The slug for the submited challenge
	 * @param  object $data          An object containing the challenge result data
	 * @param  userId $userId        Unique id for the user which submited this captcha
	 * @return object                Object to be sent back to the user containing a pass_token or new challenge
	 */
	public function validateChallenge($challengeSlug, $data, $userId = null) {
		$botProbability = $this->ci->botProbabilityCalculator;

		// throws exception or errors
		$this->getChallangeBySlug($challengeSlug)->validateChallenge($data);

		// we are still here, this means that validation didn't throw an exception and was successful
		$botProbability->solvedCaptcha($userId);

		// if we are still unsure if this is a bot or not we show him another captcha
		if(!$botProbability->isCaptchaRequired($userId)){
			return $this->getPassTokenResponse($userId);
		}

		return $this->getChallenge($userId);
	}

	/**
	 * Returns a new Pass token response object
	 * @param  userId $userId        Unique id for this user
	 * @return object                Object to be sent back to the user containing a pass_token or new challenge
   	 */
	public function getPassTokenResponse($userId = null) {
		// prevent this user from getting unlimitted pass_tokens
		$this->ci->botProbabilityCalculator->increaseProbability(0.2,$userId);
		$output = new \stdClass();
		$output->pass_token = $this->getAccessToken();

		return $output;
	}

	/**
	 * Generates and returns an access token
	 * @param  string $userId optional user id to bind token to identity
	 * @return string         access token
	 */
	function getAccessToken($userId = "") {
		$payload = new \stdClass();
		$payload->access = true;
		$payload->userId = $userId;
		$payload->validationType = 'captcha';
		return $this->getToken($payload);
	}

	/**
	 * Converts an object to a token that is save to send to the client
	 * @param  object $data Some data needed for a challenge
	 * @return string       Json encoded and encrypted token with expireTime time and unique id for validation
	 */
	public function getToken($data) {
		$data->expireTime = time() + $this->ci->config['captcha.tokenSaveMinutes'] * 60;
		$data->tokenId = uniqid(rand());
		// whitelist this token
		$this->ci->cache->put($data->tokenId,true,$this->ci->config['captcha.tokenSaveMinutes']);
		return $this->encrypt(json_encode($data));
	}



	function encrypt($string, $encrypt_method = "AES-256-CBC") {
		$encryptKey = $this->ci->config['captcha.tokenKey'];
		$iv = substr(hash('sha256', "okay" . rand() . random_bytes(10)), 0, 16);
		$key = hash('sha256', $encryptKey . $iv);
		return $iv . openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	}

	function decrypt($cipherText, $encrypt_method = "AES-256-CBC") {
		$encryptKey = $this->ci->config['captcha.tokenKey'];
		$encryptedText = substr($cipherText, 16);
		$iv = substr($cipherText, 0, 16);

		$key = hash('sha256', $encryptKey . $iv);
		// $iv = substr($cipherText, 0, 16);


		return openssl_decrypt($encryptedText, $encrypt_method, $key, 0, $iv);
	}

	/**
	 * Increases the likelyness that this user is a bot and should see a captcha
	 * @param  float   $amount [description]
	 * @param  integer $userId [description]
	 * @return [type]          [description]
	 */
	public function increaseBotLikelyness($amount = 0.1, $userId = 0) {

	}

	/**
	 * Should this software learn?
	 * Extra settings may be necessary for challenges to be able to learn
	 * @return bool True if it should be improved
	 */
	public function shouldSelfImprove(){
		return $this->ci->config['captcha.learn'];
	}
}
