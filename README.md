This provides an API and webpage to generate and validate Multiimage-Select CAPTCHAS. It can be implemented in your website or blog as well as in your mobile application.  
If you don't or can't host it yourself, you can use our endpoint `https://coflnet.com/api/v1/captcha`

## Installation
If not done allready, install a copy of [userfrosting](https://learn.userfrosting.com/installation).  
Then you will have to add this sprinkle to your `sprinkles.json`.
It should then look something like this.  
```
{
    "require": {
        "ekwav/uf_captcha": "^0.0.1"
    },
    "base": [
        "core",
        "account", // optional
        "admin", // optional
        "Captcha"
    ]
}
```
Now run `composer update; php bakery bake; php bakery migrate` in your root userfrosting directory.
This is about it for the software part. You may add some images.

## Configuration
You have some settings that you can tweak inside of `/config/default.php`. Please don't edit this directly because changes will be overwritten if you update this sprinkle but rather make a new sprinkle and  overwrite it there. How? [more information](https://learn.userfrosting.com/configuration/config-files)

## API integration
The idea is that the client (front end website or mobile app) requests a challenge from `/api/v1/captcha` via a `GET` request, the origin server on which the software is installed generates a challenge (can be customized) and retuns it. The challenge should be designed in a way that it easier for humans to solve than for bots. The, hopefully not a bot, user then completes the challenge and `POST`s it to `/api/v1/captcha/c/{captcha_slug}`, the server validates the challenge result and either returns a so called `pass_token` or another captcha if the challenge wasn't solved correctly or the probability for the user being a bot is to high.
Example pass_token response:
```
{"pass_token":"UNIQUE_TOKEN"}
```
The token can then be sent to `/api/v1/captcha/validate` via `POST` as `pass_token` which will respond with either `{'access_granted':true}`, `{'error':"token_timeout","message"::'This token timed out'}` when the token is older than ~2 minutes or  `{"error":"invalid_token","message":"This token is invalid, please request a new challenge"}` if another error occured.

## Predefined captcha
There is currently one integrated captcha which is the `multiimage-select` captcha. It is the well known captcha where you have to select images that contain a specific thing.
Example challenge response:
```
{
    "slug": "multiimage-select",
    "challenge": {
        "images": [
            "b53cb5fd4bee5a95H2DYEpnKR%2BCA0Q8mO1W3liXlCf3LQBPilL0whDI5zSu0JNXxDbvD4OtgRtI%2BXvukZ4eX%2Fqcr%2FErR7HYwbiw%2B34Rp3nyShvMW1WM%2BJPIJeD6t1%2BS7pMrSGmYf8FufuMHA",
            ... 14 more tokens ...
            "8372b1e555d9b512jornppRDq1Pa8IsI2lud61WqOHpOLIHOIF5tCNJZCrjB824cGrrQ5VNolMqhtHQXwhD46oNw%2BD3aTCc%2F%2FOvShJsoILooCZjbajmp%2BctLbO8nag45rggLEkZeIv4%2F%2F3kiuTFvNTl8yJRR5bJqxzBz%2BA%3D%3D"
        ],
        "target": "dog",
        "token": "7a2677459ef2587ci1njzI29acBmtdHizMmP5k7VGbo4lfwl2nRwdcDYdKP4ziZdFYAkvPAqBAIEtkfJPUgBPmtumOLRdhXefLbhi2%2BVTwEJkj9EuTX%2BNPyulIk%3D"
    }
}
```
Example body for submission:
```
{
    "tokens":[
            "b53cb5fd4bee5a95H2DYEpnKR%2BCA0Q8mO1W3liXlCf3LQBPilL0whDI5zSu0JNXxDbvD4OtgRtI%2BXvukZ4eX%2Fqcr%2FErR7HYwbiw%2B34Rp3nyShvMW1WM%2BJPIJeD6t1%2BS7pMrSGmYf8FufuMHA",
            ... 14 more tokens ... "8372b1e555d9b512jornppRDq1Pa8IsI2lud61WqOHpOLIHOIF5tCNJZCrjB824cGrrQ5VNolMqhtHQXwhD46oNw%2BD3aTCc%2F%2FOvShJsoILooCZjbajmp%2BctLbO8nag45rggLEkZeIv4%2F%2F3kiuTFvNTl8yJRR5bJqxzBz%2BA%3D%3D"
        ],
        "result":[true,true,true,false,true,true,true,true,true,false,true,true,true,true,true,true],
        "target":"dog",
        "target_token":"7a2677459ef2587ci1njzI29acBmtdHizMmP5k7VGbo4lfwl2nRwdcDYdKP4ziZdFYAkvPAqBAIEtkfJPUgBPmtumOLRdhXefLbhi2%2BVTwEJkj9EuTX%2BNPyulIk%3D"}
```
The `result` array should contain the selection wherever that token contains a, for this example `dog`.

### Custom captchas
You can define custom captcha types to further improve security, after all a bot might be good at one task but not at many. Your Captcha has to have a unique `slug` to handle the challenge data properly.
You may check if an implementation exists on the client side. If not, the user should be redirected to a custom web-implementation. (not implemented yet)

## User convenience (skipping captchas)
Whatever we do and whatever captchas we design, they will still feel somewhat added on top of your application. Thats why I decided to include something called `BotProbabilityCalculator` which looks up the userId or IP-adress and checks if the user has completed a captcha recently and if so return a `pass_token` on the first request instead of a captcha.
