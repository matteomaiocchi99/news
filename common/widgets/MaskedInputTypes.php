<?php

namespace common\widgets;

use common\util\ArrayUtil;
use Yii;
use yii\widgets\MaskedInput;

/**
 * Alert widget renders a message from session flash. All flash messages are displayed
 * in the sequence they were assigned using setFlash. You can set message as following:
 *
 * ```php
 * Yii::$app->session->setFlash('error', 'This is the message');
 * Yii::$app->session->setFlash('success', 'This is the message');
 * Yii::$app->session->setFlash('info', 'This is the message');
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 * Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @author Alexander Makarov <sam@rmcreative.ru>
 */
class MaskedInputTypes extends MaskedInput
{
	const TYPE_DATE = "date";
	const TYPE_DATE_TIME = "datetime";
	const TYPE_FLOAT = "float";
	const TYPE_HOUR = "hour";

	const CONFIGS = [
		self::TYPE_DATE => [
			'clientOptions' => ['alias' => 'date'],
			'options' => ['class' => 'date form-control']
		],
		self::TYPE_DATE_TIME => [
			'mask' => "1/2/y h:s",
			'clientOptions' => ['alias' => 'datetime'],
			'options' => ['class' => 'date form-control'],
		],
		self::TYPE_FLOAT => [
			"clientOptions" => [
				"alias" => "decimal",
				"digits" => 2,
				"digitsOptional" => true,
				"radixPoint" => ",",
				"groupSeparator" => ".",
				"autoGroup" => true,
				"removeMaskOnSubmit" => true,
				"rightAlign" => false
			]
		],
		self::TYPE_HOUR => [
			'mask' => 'h:m',
			'definitions' => [
				'h' => [
					'cardinality' => 2,
					'prevalidator' => [
						['validator' => '^([0-2])$', 'cardinality' => 1],
						['validator' => '^([0-9]|0[0-9]|1[0-9]|2[0-3])$', 'cardinality' => 2],
					],
					'validator' => '^([0-9]|0[0-9]|1[0-9]|2[0-3])$'
				],
				'm' => [
					'cardinality' => 2,
					'prevalidator' => [
						['validator' => '^(0|[0-5])$', 'cardinality' => 1],
						['validator' => '^([0-5]?\d)$', 'cardinality' => 2],
					]
				]
			], 'options' => ['class' => 'form-control form-masked-input']]
	];

	/**
	 * {@inheritdoc}
	 */
	public static function widget($config = [])
	{
		if (empty($config["type"]) || empty(self::CONFIGS[$config["type"]])) {
			throw new NotFoundHttpException("Tipo input non trovato");
		}

		$config_final = self::CONFIGS[$config["type"]];

		unset($config["type"]);

		$config_final = ArrayUtil::array_merge_recursive($config_final, $config);


		return parent::widget($config_final);
	}
}
