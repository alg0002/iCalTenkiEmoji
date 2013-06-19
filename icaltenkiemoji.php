<?php
	ini_set('default_charset', 'UTF-8');

	// デフォルトの表示地域…東京
	$pref = '13';
	$area = '63';
	// パラメーターに表示地域情報が含まれていたらセット
	if (isset($_GET['p'])) {
		$pref = htmlspecialchars($_GET['p'], ENT_QUOTES, 'UTF-8');
	}
	if (isset($_GET['a'])) {
		$area = htmlspecialchars($_GET['a'], ENT_QUOTES, 'UTF-8');
	}
	// icalデータの取得
	$info = file_get_contents("http://weather.livedoor.com/forecast/ical/".$pref."/".$area.".ics");
	$info = explode("\n", $info);
	$cnt = count($info);
	// SUMMARY行を編集しながら出力
	for ($i=0; $i<$cnt; $i++) {
		if (substr($info[$i],0,8) === 'SUMMARY:') {
			$info[$i] = str_replace("晴", "☀", $info[$i]);
			$info[$i] = str_replace("雨", "☔", $info[$i]);
			$info[$i] = str_replace("曇り", "☁", $info[$i]);
			$info[$i] = str_replace("曇", "☁", $info[$i]);
			$info[$i] = str_replace("雪", "⛄", $info[$i]);
			$info[$i] = str_replace("時々", "|", $info[$i]);
			$info[$i] = str_replace("のち", ">", $info[$i]);
		}
		print $info[$i]."\n";
	}
?>