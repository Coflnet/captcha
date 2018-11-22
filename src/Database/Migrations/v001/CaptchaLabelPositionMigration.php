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
 * Captcha Label positions used to store information on where something is in an image
 * important to note is that it is possible that we only have a fragment and another part was selected
 * in another captcha tile therefor the content of this tabel has to be combined
 * Version 0.0.1
 *
 * @extends Migration
 * @author Äkwav (https://coflnet.com/ekwav)
 */
class CaptchaLabelPositionMigration extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('captcha_label_bounding_box')) {
            $this->schema->create('captcha_label_bounding_box', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('captcha_image_label_id');
                $table->integer('xmin');
                $table->integer('ymin');
                $table->integer('xmax');
                $table->integer('ymax');
                $table->integer('votes')->description('how many captchas were solved with this part of the image included for a given label');
                $table->timestamps();
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('captcha_label_bounding_box');
    }
}
