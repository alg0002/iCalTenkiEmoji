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
	// 気温を華氏で表示する？
	$showinfer = '0';
	if (isset($_GET['f'])) {
		$showinfer = htmlspecialchars($_GET['f'], ENT_QUOTES, 'UTF-8');
	}
	// icalデータの取得
	$info = file_get_contents("http://weather.livedoor.com/forecast/ical/".$pref."/".$area.".ics");
	$info = explode("\n", $info);
	$cnt = count($info);
	// SUMMARY行を編集しながら出力
	for ($i=0; $i<$cnt; $i++) {
		if (substr($info[$i],0,8) === 'SUMMARY:') {
			// 最高・最低気温
			$temps = explode('/', substr($info[$i], strrpos($info[$i], ' ')+1));
			$tempmax = str_replace("℃", "", $temps[0]);
			$tempmin = str_replace("℃", "", $temps[1]);
			if ($showinfer !== '0') {
				// 気温を華氏で表示
				$tempmax = round($tempmax * 9 / 5 + 32);
				$tempmin = round($tempmin * 9 / 5 + 32);
			}
			// 気温表記を一旦削除
			$info[$i] = substr($info[$i], 0, strrpos($info[$i], ' '));
			// 各種変換
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
			if ($showmaxtemp === '1' && $showmintemp === '1') {
				// 最高/最低
				$info[$i].= ' '.$tempmax.'/'.$tempmin;
			} elseif ($showmaxtemp === '1') {
				// 最高だけ
				$info[$i].= ' '.$tempmax;
			} elseif ($showmintemp === '1') {
				// 最低だけ
				$info[$i].= ' '.$tempmin;
			}
			if ($showmaxtemp === '1' || $showmintemp === '1') {
				if ($showinfer === '0') {
					$info[$i].= '℃';
				} else {
					$info[$i].= '°F';
				}
			}
		}
		print $info[$i]."\n";
	}
?>