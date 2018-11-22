<?php
/**
 *
 * @copyright Copyright (c) 2018 Ekwav (Coflnet)
 */
namespace UserFrosting\Sprinkle\Captcha\Captcha\Probability;


use UserFrosting\Sprinkle\Captcha\Captcha\Interfaces\BotProbabilityInterface;
use Carbon\Carbon;

/**
 * Implements the BotProbabilityInterface via the userfrosting cache service
 * probability starts at 2 and is increased with any suspicious actions
 * Will tell the captcha service to show a captcha if the probability is greater
 * than 1
 * when a captcha is solved the IP probability will be reduced by * 0.8 - 0.2
 * the user probability will be reduced by * 0.6 - 0.3
 * This allows users to get by after one captcha and non users will have to pass 2
 */
class BotProbabilityCalculator implements BotProbabilityInterface {

	/**
	 * Userfrosting service container
	 */
	protected $ci;

	/**
	 * Prefix for storing probability in the cache
	 * @var string
	 */
	protected $cachePrefix = "captcha_p";

	/**
	 * IP adress of the requesting user used instead of his id
	 * @var string
	 */
	protected $remoteIp;

	/**
	 * Constructs a new ProbabilityCalculator
	 * @param string $remoteIp IPv4 or IPv6 ip adress of this user
	 * @param object $ci       Userfrosting service container
	 */
	public function __construct($remoteIp,$ci) {
		$this->ci = $ci;
		$this->remoteIp = $remoteIp;
	}

	/**
	 * {@inheritDoc}
	 */
	public function increaseProbability($amount = 0.1, $userId = "") {
		$this->addToCache($this->remoteIp,$amount);
		if($userId != ""){
			$this->addToCache($userId,$amount);
		}
	}

	/**
	 * Increases the probability in the cache
	 * @param string $key    User or IP-Adress for which to get the probability
	 * @param float  $amount How much to increase the value
	 */
	protected function addToCache($key,$amount){
		if($amount < 0){
			throw new \Exception("can't add negative amount to probability");
		}
		$oldScore = $this->getProbability($key);
		$newScore = $oldScore + $amount;
		$this->setProbability($key,$newScore);
	}

	/**
	 * {@inheritDoc}
	 */
	public function solvedCaptcha($userId = "") {
		$ipScore = $this->getProbability($this->remoteIp);
		$newIpScore = $ipScore * 0.8 - 0.2;
		$this->setProbability($this->remoteIp,$newIpScore);

		if($userId == ""){
			return;
		}
		// the userScore gets down faster
		$userScore = $this->getProbability($userId);
		$newUserScore = $userScore * 0.6 - 0.3;
		$this->setProbability($userId,$newUserScore);
	}

	/**
	 * {@inheritDoc}
	 */
	public function isCaptchaRequired($userId = null) {
		$ipScore = $this->getProbability($this->remoteIp);
		if($ipScore <= 1) {
			return false;
		} else if(is_null($userId)) {
			return true;
		} else {
			$userScore = $this->getProbability($userId);
			if($userScore <= 1) {
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * Reads a probability score from the cache and returns it, will return default probability if user wasn't found
	 * @param  string $identifier User or IP-Adress for which to get the probability
	 * @return float              The probability for this identifier
	 */
	protected function getProbability($identifier){
		return $this->ci->cache->get($this->cachePrefix . $identifier,$this->ci->config['captcha.probability.default']);
	}

	/**
	 * Sets a new probability for a user
	 * @param string $identifier User or IP-Adress for which to set the probability
	 * @param float  $score      What to set the score
	 */
	protected function setProbability($identifier,$score){
		if($score < 0) {
			$score = 0;
		}
		$expiresAt = Carbon::now()->addDays($this->ci->config['captcha.probability.saveDays']);
		return $this->ci->cache->put($this->cachePrefix . $identifier,$score,$expiresAt);
	}
}
