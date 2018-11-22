<?php
/**
 *
 * @copyright Copyright (c) 2018 Ekwav (Coflnet)
 */
namespace UserFrosting\Sprinkle\Captcha\Captcha\Challenges;

use UserFrosting\Sprinkle\Captcha\Captcha\ChallengeInterface;
use UserFrosting\Sprinkle\Captcha\Database\Models\CaptchaLabel;
use UserFrosting\Sprinkle\Captcha\Database\Models\CaptchaImageLabel;
use UserFrosting\Sprinkle\Captcha\Database\Models\CaptchaImage;
use UserFrosting\Sprinkle\Captcha\Api\Exception\MissingOrInvalidFieldException;
use UserFrosting\Sprinkle\Captcha\Api\Exception\InvalidLabelException;
use UserFrosting\Sprinkle\Captcha\Api\Exception\NotEnoughImageTargetsException;



class ImageSelectChallenge implements ChallengeInterface {

	/**
	 * reference to the captcha class
	 * @var Captcha
	 */
	protected $captcha;

	public $hi = "hi";

	public function __construct($captcha = null) {
		$this->captcha = $captcha;
	}

	/**
	* {@inheritDoc}
	*/
	public function newChallenge(){
		$labels = $this->getLabels();
		$targetLabel = $labels[random_int(0, count($labels) - 1)];
		$output->images = $this->generateImages($targetLabel,$labels);
		$output->target = $targetLabel;
		$output->token = urlencode($this->getLabelToken($targetLabel,$installId));
		return $output;
	}

	/**
	* {@inheritDoc}
	*/
    public function validateChallenge($data){
		$tokens = $data['tokens'];
		$result = $data['result'];


		if(is_null($data['target_token'])){
			throw new MissingOrInvalidFieldException('target_token');
		}
		$targetTokenData = $this->getLabelTokenData(urldecode($data['target_token']));
		$target = $targetTokenData->label;



		if(is_null($target) || $target != $data['target']){
			throw new InvalidLabelException();
		}

		if(is_null($data)){
		    throw new MissingOrInvalidFieldException('all fields');
		}

		if(is_null($tokens) || !is_array($tokens) || count($tokens)<16){
		    throw new MissingOrInvalidFieldException('tokens');
		}

		if(is_null($result) || !is_array($result) || count($result) < 16){
		    throw new MissingOrInvalidFieldException('result');
		}



		$oneWrong = false;
		$correctCount = 0;
		$unknownImagesIds = [];


		// we now know that the label should be contained in two or more images
		for ($index = 0; $index < 16; $index++) {
			$tokenData = $this->captcha->getTokenData(urldecode($tokens[$index]));
			$imageLabel = $tokenData->label;
		    if($imageLabel == $target){
		        if($result[$index]){
		            $correctCount ++;
		        } else if($oneWrong){
		            throw new InvalidLabelException();
		        } else {
		            $oneWrong = true;
		        }
		    } else if($imageLabel == 'unknown') {
				if($result[$index]){
		            // yay, we just got labeled as this categorie
		            // BUT it could still be a bot clicking random images
		            // so before storing this label vertify the rest!
		            array_push($unknownImagesIds,$tokenData->name);
		        }
			} else {
		        if($result[$index]){
		            if($oneWrong){
		                throw new InvalidLabelException();
		            } else {
		                 $oneWrong = true;
		            }
		        }
		    }
			// we are done, to prevent replay attacks with the same token we now invoke it
			$this->captcha->invokeToken($tokenData->tokenId);
		}

		if($correctCount <2){
		    throw new InvalidLabelException();
		}

		// looks like this is a real human, horray!
		// now store the unknown images
		foreach ($unknownImagesIds as $value) {
			$this->addLabelVote($target,$value);
		}


		return true;
	}


	/**
	 * Prints out file content
	 * @param  object $data Token data
	 */
	public function getAdditionalData($data){
		// escape relative path
		$escapedPath = preg_replace('/\.\./m','\.\.',$data->name);
		$path = $this->getImagePath() . '/' . $escapedPath;
		$image_mime = image_type_to_mime_type(exif_imagetype($path));

		header("Content-type: " . $image_mime);
		echo @file_get_contents($path);

		return $image_mime;
	}

	/**
	 * Registers a new vote for an image for a given label
	 * @param string $label The label which was requested
	 * @param string $image The imagename
	 */
	private function addLabelVote($label,$image){
		// get the image and label id by name
		$label = CaptchaLabel::firstOrCreate(['label' => $label]);
		$image = CaptchaImage::firstOrCreate(
				    ['name' => $image], ['user_label' => 1]);

		$imageLabel = CaptchaImageLabel::firstOrCreate(
					['captcha_image_id'=>$image->id,'captcha_label_id'=>$label->id],
					['votes'=>0]);


		$imageLabel->increment('votes');

	//	$imageLabel->save();

	}

	/**
	* {@inheritDoc}
	*/
	public function getSlug(){
		return "multiimage-select";
	}


	/**
	* Generates new array of images for the client to clasify
	* there are atleast two of the desired categorie and two that are `black`
	*/
	function generateImages($targetLabel, $availableLabels) {

	    // prefill the array with the desired output
	    $resultImages = array_fill(0, 16, $this->getImageTokenFromCategorie($targetLabel));

	    $tileCount = 16;

		// to have atleast two images from this categorie
		$resultImages[0] = $this->getImageTokenFromCategorie($targetLabel);
		$resultImages[1] = $this->getImageTokenFromCategorie($targetLabel);


	    for ($index1 = 2; $index1 < $tileCount; $index1++) {
			// select a random categorie
	        $categorie = $availableLabels[random_int(0, count($availableLabels) - 1)];
			// get a token for this categorie
	        $resultImages[$index1] = $this->getImageTokenFromCategorie($categorie);
	    }

		if($this->captcha->shouldSelfImprove()){
			// we want to label unknown images
			$resultImages[2] = $this->getImageTokenFromCategorie('unknown');
			$resultImages[3] = $this->getImageTokenFromCategorie('unknown');
		}
	    // shuffle the array
	    $shuffled = $this->suffle($resultImages);

	    return $shuffled;
	}

	/**
	 * Returns a valid image token for a given categorie
	 * @param  string $categorie the desired categorie
	 * @return string            base64 url encoded token
	 */
	private function getImageTokenFromCategorie($categorie){
		$imagePath = $this->getImageWithCategorie($categorie);
		return urlencode($this->getFileToken($imagePath,$categorie));
	}

	/**
	 * Returns a random image from within a given categorie
	 * as path relative to $this->getImagePath
	 * @param  string $categorie categorie name
	 * @return string           path to image
	 */
	private function getImageWithCategorie($categorie){
		$files = glob($this->getImagePath() . '/' . $categorie . '/*.*');


		// if you find this in your stacktrace it is likely that your folder doesn't contain any files
		// Put some images in there to fix it
		$randomPaht = $files[$this->captcha->getRandomArrIndex($files)];


		// since we want the path to be relative to our image directory
		// we remove the absolute path and prepend it with the categorie
		$imagePath = $categorie . '/' . basename($randomPaht);
		return $imagePath;
	}

	/**
	 * Shuffles a given array by switching indexes randomly
	 * @param  array $resultImages the array which to shuffle
	 * @return array               the shuffled array
	 */
	function suffle($resultImages) {
	    for ($i = 0; $i < count($resultImages); $i++) {
	        $tmp = $resultImages[$i];
	        $index = $this->captcha->getRandomArrIndex($resultImages);
	        $resultImages[$i] = $resultImages[$index];
	        $resultImages[$index] = $tmp;
	    }
	    return $resultImages;
	}



	function getLabelToken($label, $installId) {
	    $payload = new \stdClass();
		$payload->label = $label;
	    return $this->captcha->getToken($payload);
	}

	function getLabel($token) {
	    return $this->getLabelTokenData($token)->label;
	}

	function getLabelTokenData($token) {
	    return $this->captcha->getTokenData($token);
	}

	/**
	 * Generates and returns a token that contains file path and label
	 * @param  string $fileName path relative to getImagePath
	 * @param  sting $label     label for this image
	 * @return string           Encrypted token for save transmition to user
	 */
	function getFileToken($fileName, $label) {
	    $payload = new \stdClass();
		$payload->label = $label;
		$payload->name = $fileName;

	    return $this->captcha->getToken($payload);
	}



	function getLabels() {
		$folders = glob($this->getImagePath() . '/*', GLOB_ONLYDIR);
		// remove the path
		foreach ($folders as $key => $value) {
			$folders[$key] = basename($folders[$key]);
			if($folders[$key] == 'unknown'){
				unset($folders[$key]);
			}
		}
		if(count($folders)<=1){
			throw new NotEnoughImageTargetsException();
		}
		return $folders;
	}


	/**
	* The local folder that contains unlabeled images
	*/
	public function getUnknownImagePath(){
		return getImagePath() . "/unknown";
	}

	/**
	* The local folder that contains labeled and unlabeled images
	*/
	public function getImagePath(){
		return \UserFrosting\ROOT_DIR . '/' . \UserFrosting\APP_DIR_NAME . "/captcha/images";
	}

	function decide($userId,$header,$referer,$domain,$captchaSubmission){

	}
}
