<?php
/**
 * Coflnet (https://coflnet.com)
 *
 * @link   https://coflnet.com/docs/sprinkle/uf_captcha
 * @license captcha-license-later
 */
namespace UserFrosting\Sprinkle\Captcha\Database\Models;

use Illuminate\Database\Capsule\Manager as Capsule;
use UserFrosting\Sprinkle\Core\Database\Models\Model;
/**
 * Holds image information
 *
 * @author Ekwav (https://coflnet.com/ekwav)
 */
class CaptchaImageLabel extends Model
{
    /**
     * @var string The name of the table for the current model.
     */
    protected $table = "captcha_image_label";

    protected $hidden = ['id'];

    protected $fillable = [
        "captcha_image_id",
        "captcha_label_id",
        "votes"
    ];

    /**
     * @var bool Enable timestamps for Verifications.
     */
    public $timestamps = true;

}
