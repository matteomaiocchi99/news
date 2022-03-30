<?php

$config = [
    //Aggiungo il campo lingua e timeZone
    'language' => "it",
    'timeZone' => "Europe/Rome",
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '',
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'appendTimestamp' => true, //Включаем поддержку версионности
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,   // do not publish the bundle
                    'js' => [
                        'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js',
                    ],
                    'jsOptions' => [ 'position' => \yii\web\View::POS_HEAD ],
                ],
                /*'yii\bootstrap\BootstrapAsset' => [ //stiamo dicendo di escludere i bootstrap di yii in questo modo
                    'css' => [],
                    'js' => [],
                ],*/
            ],
            'linkAssets' => true,
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*']
    ];
}

return $config;
