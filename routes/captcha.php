<?php

$app->post('/api/v1/captcha/c/{slug}', 'UserFrosting\Sprinkle\Captcha\Controller\CaptchaController:validateChallenge');
$app->get('/api/v1/captcha', 'UserFrosting\Sprinkle\Captcha\Controller\CaptchaController:getChallenge');
$app->get('/api/v1/captcha/c/{type}/data/{token}', 'UserFrosting\Sprinkle\Captcha\Controller\CaptchaController:getAdditionalData');
