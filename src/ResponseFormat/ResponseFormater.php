<?php

/**
 * Coflnet (https://coflnet.com)
 *
 * @link   https://coflnet.com/docs/sprinkle/uf_captcha
 * @license captcha-license-later
 */
namespace UserFrosting\Sprinkle\Captcha\ResponseFormat;



class ResponseFormater {
	/**
	 * Prints an error response
	 *
	 * @param  [type]  $response     Slim response object
	 * @param  string  $errorSlug    Unique error-code identifying this error
	 * @param  string  $message      Description of what got wrong or what could have caused this error
	 * @param  string  $userMessage  Optional user message
	 * @param  integer $responseCode Http response code to return
	 * @param  string  $link         Link for more information
	 * @return void                  [description]
	 */
	public function error($response,$errorSlug,$message,$userMessage = null,$responseCode = 400,$link = null){
		$output = new \stdClass();
		$output->error = $errorSlug;
		$output->message = $message;
		if($userMessage != null)
			$output->user_message = $userMessage;
		if($link != null)
			$output->info = $link;

		return $response->withJson($output)->withStatus($responseCode);
	}
}
