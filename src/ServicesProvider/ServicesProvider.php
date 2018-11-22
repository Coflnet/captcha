<?php
/**
 * UserFrosting (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/UserFrosting
 * @license   https://github.com/userfrosting/UserFrosting/blob/master/licenses/UserFrosting.md (MIT License)
 */
namespace UserFrosting\Sprinkle\Captcha\ServicesProvider;


use UserFrosting\Sprinkle\Captcha\ResponseFormat\ResponseFormater;
use UserFrosting\Sprinkle\Captcha\Captcha\Captcha;
use UserFrosting\Sprinkle\Captcha\Captcha\Challenges\ImageSelectChallenge;
use UserFrosting\Sprinkle\Captcha\Captcha\Probability\BotProbabilityCalculator;

/**
 * Registers services for the captcha sprinkle
 *
 * @author Äkwav  ekwav@coflnet.com
 */
 class ServicesProvider
 {
     public function register($container)
     {
         $container->extend('classMapper', function ($classMapper, $c) {
             $classMapper->setClassMapping('invitations', 'UserFrosting\Sprinkle\Builder\Database\Models\Invitations');
             return $classMapper;
         });

         $container->extend('errorHandler', function ($handler, $c) {
             // Register the MissingOwlExceptionHandler
             $handler->registerHandler('\UserFrosting\Sprinkle\Captcha\Api\Exception\ApiException', '\UserFrosting\Sprinkle\Captcha\Api\Handler\ApiExceptionHandler');
             $handler->registerHandler('\UserFrosting\Sprinkle\Captcha\Api\Exception\TokenTimoutException', '\UserFrosting\Sprinkle\Captcha\Api\Handler\ApiExceptionHandler');

             return $handler;
         });

         $container['captcha'] = function ($c) {
             $captcha = new Captcha($c);
             $captcha->addChallange(new ImageSelectChallenge($captcha));
             return $captcha;
         };

         $container['responseFormater'] = function ($c) {
             $formatter = new ResponseFormater($c);
             return $formatter;
         };

         $container['botProbabilityCalculator'] = function ($c) {
             $remoteIp = $_SERVER['REMOTE_ADDR'];
             $calculator = new BotProbabilityCalculator($remoteIp,$c);
             return $calculator;
         };
     }
 }
