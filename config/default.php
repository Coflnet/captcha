<?php

    /**
    *SamplesiteconfigurationfileforUserFrosting.Youshoulddefinitelysetthesevalues!
    *
	*/
	return[
		'captcha' =>[
			'tokenKey' => 'fdrVePCeWTY7eLNu9gNWRw9CThUZZdCr4e2ALFFyKYxrUvprUMfLF',
            'tokenSaveMinutes' => 2,
            'probability' => [
                'default' => 2,
                'saveDays' => 7
            ],
            // classify unknown images
            'learn' => true
		]
	];
