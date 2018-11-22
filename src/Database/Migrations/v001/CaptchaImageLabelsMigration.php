<?php
/**
 * Coflnet (https://coflnet.com)
 *
 * @link   https://coflnet.com/docs/sprinkle/uf_captcha
 */
namespace UserFrosting\Sprinkle\Captcha\Database\Migrations\v001;


use UserFrosting\System\Bakery\Migration;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Schema\Blueprint;
/**
 * Captcha Labels
 * Version 0.0.1
 *
 * @extends Migration
 * @author Äkwav (https://coflnet.com/ekwav)
 */
class CaptchaImageLabelsMigration extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('captcha_image_label')) {
            $this->schema->create('captcha_image_label', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('captcha_image_id');
                $table->integer('captcha_label_id');
                $table->integer('votes')->description('how many captchas were solved with this image included for a given label');
                $table->timestamps();
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('captcha_image_label');
    }
}
