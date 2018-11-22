<?php
/**
 *
 * @copyright Copyright (c) 2018 Ekwav (Coflnet)
 */
namespace UserFrosting\Sprinkle\Captcha\Captcha\Interfaces;

interface BotProbabilityInterface {

	/**
	 * Increases the probability that a given user is a bot
	 * @param  float   $amount How much to add to the probability
	 * @param  string $userId  Optional user id if present, otherwise the ip should be taken as userId
	 */
	public function increaseProbability($amount = 0.1, $userId = "");


	/**
	 * Should reduce the bot probability
	 * @param  string $userId  Optional userId to consider if ip is bad
	 */
	public function solvedCaptcha($userId = "");


	/**
	 * Returns a bool which says if the user has to solve another captcha
	 * @param  string $userId  Optional userId to consider if ip is bad
	 * @return boolean         Is true if a captcha should be shown
	 */
	public function isCaptchaRequired($userId = "");


}
