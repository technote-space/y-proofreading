<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'Y_PROOFREADING' ) ) {
	exit;
}

return [

	// request url
	'request_url' => 'https://jlp.yahooapis.jp/KouseiService/V1/kousei',

	// filter
	'filter'      => [
		'誤変換'         => [
			'index'       => 1,
			'description' => '人事異同→人事異動',
		],
		'誤用'          => [
			'index'       => 2,
			'description' => '煙に巻く→けむに巻く',
		],
		'使用注意'        => [
			'index'       => 3,
			'description' => '外人墓地→外国人墓地',
		],
		'不快語'         => [
			'index'       => 4,
			'description' => 'がんをつける→にらむ',
		],
		'機種依存または拡張文字' => [
			'index'       => 5,
			'description' => '○付き数字、一部の旧字体など　※EUC表示不可の場合も指摘されます',
		],
		'外国地名'        => [
			'index'       => 6,
			'description' => 'モルジブ→モルディブ',
		],
		'固有名詞'        => [
			'index'       => 7,
			'description' => 'ヤフーブログ→Yahoo!ブログ',
		],
		'人名'          => [
			'index'       => 8,
			'description' => 'ベートーヴェン→ベートーベン',
		],
		'ら抜き'         => [
			'index'       => 9,
			'description' => '食べれる→食べられる',
		],
		'当て字'         => [
			'index'       => 10,
			'description' => '出鱈目、振り仮名',
		],
		'表外漢字あり'      => [
			'index'       => 11,
			'description' => '灯籠→灯●',
		],
		'用字'          => [
			'index'       => 12,
			'description' => '曖昧→あいまい',
		],
		'用語言い換え'      => [
			'index'       => 13,
			'description' => 'セロテープ→セロハンテープ　※商標など',
		],
		'二重否定'        => [
			'index'       => 14,
			'description' => '聞かなくはない',
		],
		'助詞不足の可能性あり'  => [
			'index'       => 15,
			'description' => '学校行く',
		],
		'冗長表現'        => [
			'index'       => 16,
			'description' => 'ことができます',
		],
		'略語'          => [
			'index'       => 17,
			'description' => 'ADSL→非対称デジタル加入者線(ADSL)',
		],
	],
];
