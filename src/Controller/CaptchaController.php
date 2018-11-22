<?php
/**
 *
 * @copyright Copyright (c) 2018 Ekwav (Coflnet)
 */
namespace UserFrosting\Sprinkle\Captcha\Controller;

use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use Carbon\Carbon;
use UserFrosting\Fortress\RequestDataTransformer;
use UserFrosting\Fortress\RequestSchema;
use UserFrosting\Fortress\ServerSideValidator;
use UserFrosting\Fortress\Adapter\JqueryValidationAdapter;
use UserFrosting\Sprinkle\Schule\Database\Models\DVGuesses;
use UserFrosting\Support\Exception\ForbiddenException;

use Hashids\Hashids;


class CaptchaController extends SimpleController
{
    public function getChallenge($request, $response, $args)
    {
		//$params = $request->getParsedBody();
        //$data = $this->ci->api->validateData($params,'schema://requests/captcha/new.yaml');
		return $response->withJson($this->ci->captcha->getChallenge());
    }


	public function validateChallenge($request, $response, $args)
	{
        $params = $request->getParsedBody();

        return $response->withJson($this->ci->captcha->validateChallenge($args['slug'],$params));
    }


    /**
     * Proxies the request to the Captcha class which does the validation
     */
	public function validateToken($request, $response, $args)
	{
		$params = $request->getParsedBody();
        $output = $this->ci->captcha->validateChallenge($params);
		return $response->withJson($output);
	}


	public function getAdditionalData($request, $response, $args)
	{
        $type = $args['type'];
        $token = $args['token'];
        $mimeType = $this->ci->captcha->getAdditionalData($type,$token);

        return $response->withHeader("Content-Type", $mimeType);
	}
}
