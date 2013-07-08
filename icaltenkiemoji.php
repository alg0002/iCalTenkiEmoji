<?php
	ini_set('default_charset', 'UTF-8');

	header("Cache-Control: no-cache");
	header("Content-Type: text/plain; charset=utf-8");

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
	// 時々とのちの表記パターン
	$displaypattern = '0';
	if (isset($_GET['d'])) {
		$displaypattern = htmlspecialchars($_GET['d'], ENT_QUOTES, 'UTF-8');
	}
	// 最高気温を表示しない？
	$showmaxtemp = '1';
	if (isset($_GET['m'])) {
		$showmaxtemp = htmlspecialchars($_GET['m'], ENT_QUOTES, 'UTF-8');
	}
	// 最低気温を表示しない？
	$showmintemp = '1';
	if (isset($_GET['n'])) {
		$showmintemp = htmlspecialchars($_GET['n'], ENT_QUOTES, 'UTF-8');
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
			if ($displaypattern === '0') {
				$info[$i] = str_replace("時々", "", $info[$i]);
				$info[$i] = str_replace("のち", "/", $info[$i]);
			} else {
				$info[$i] = str_replace("時々", "|", $info[$i]);
				$info[$i] = str_replace("のち", "→", $info[$i]);
			}
			// 最高・最低気温
			if ($showmaxtemp === '1' && $showmintemp === '1') {
				// そのまま
			} elseif ($showmaxtemp === '1') {
				// 最高だけ
				$info[$i] = substr($info[$i], 0, strrpos($info[$i], '/'));
			} elseif ($showmintemp === '1') {
				// 最低だけ
				$info[$i] = substr($info[$i], 0, strrpos($info[$i], ' ')+1).
							substr($info[$i], strrpos($info[$i], '/')+1, strlen($info[$i])-strrpos($info[$i], '/')-1);
			} else {
				// 気温なし
				$info[$i] = substr($info[$i], 0, strrpos($info[$i], ' '));
			}
		}
		print $info[$i]."\n";
	}
?>