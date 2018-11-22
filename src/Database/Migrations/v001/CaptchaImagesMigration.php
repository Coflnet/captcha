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
 * Captcha Image
 * Version 0.0.1
 *
 * @extends Migration
 * @author Äkwav (https://coflnet.com/ekwav)
 */
class CaptchaImagesMigration extends Migration
{
    /**
     * {@inheritDoc}
     */
    public function up()
    {
        if (!$this->schema->hasTable('captcha_image')) {
            $this->schema->create('captcha_image', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->description('exact name of this image in the folder');
                $table->integer('user_label')->description('label that the user set, needs to be validated (untrusted)');
                $table->integer('label')->nullable()->description('label that we think is accurate, based on settings in the config file');
                $table->string('coflnet_img_id',32)->default('')
                ->description('Unique identifier inside of the coflnet system for this image, needed for syncing votes');
                $table->timestamps();
            });


            // create the direcotrie in which to store the images
            mkdir(\UserFrosting\APP_DIR_NAME . "/captcha/images/unknown",0775,true);
            chown(\UserFrosting\APP_DIR_NAME . "/captcha/images/unknown", "www-data:www-data");
            chown(\UserFrosting\APP_DIR_NAME . "/captcha/images", "www-data:www-data");
        }
    }



    /**
     * {@inheritDoc}
     */
    public function down()
    {
        $this->schema->drop('captcha_image');

        echo "\n You may want to delete the image direcotry \n";
    }
}
