<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Sprinkle\Captcha\Api\Handler;
use UserFrosting\Sprinkle\Core\Error\Handler\HttpExceptionHandler;
/**
 * Handler for Api Exceptions.
 *
 * prints a formated api response for some error
 * because this Exception is meant for external APIs we don't ever render a html page
 * @author Ekwav (https://coflnet.com/ekwav)
 */
class ApiExceptionHandler extends HttpExceptionHandler
{
    /**
     * Custom handling for requests that did not pass authentication.
     */
    public function handle()
    {
        return $this->renderGenericResponse();
    }

    /**
     * Render a detailed response with debugging information.
     * this isn't different from handle
     *
     * @return ResponseInterface
     */
    public function renderDebugResponse()
    {
        return $this->handle();
    }

    /**
     * Render a generic, user-friendly response without sensitive debugging information.
     *
     * @return ResponseInterface
     */
    public function renderGenericResponse()
    {
        $exception = $this->exception;

        $errorSlug = $exception->getErrorSlug();
        $message = $exception->getDefaultMessage();
        $userMessage = $this->ci->translator->translate($exception->getUserMessage());
        $statusCode = $exception->getHttpErrorCode();
        $link = $exception->getLink();

        return $this->ci->responseFormater->error($this->response,$errorSlug,$message,$userMessage,$statusCode,$link);
    }




}
