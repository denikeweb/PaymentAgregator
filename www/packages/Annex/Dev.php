<?php
	/**
	 * @author Denis Dragomiric <den@lux-blog.org>
	 * @version Pagen 1.1.6
	 */

namespace Annex;


class Dev {

	/**
	 * Print array with tabulation
	 *
	 * @param $array
	 */
	public static function showArray ($array) {
		echo '<pre style="white-space: pre-wrap; overflow: hidden; text-align: left;"><code class="php">';
			print_r($array);
		echo '</code></pre>';
	}

	public static function formatNumber ($number) {
		$_str = '';
		$n = strlen($number) - 1;
		for ($i = $n; $i >= 0; $i --) {
			$_str .= $number [$i];
			if (($n - $i) % 3 == 2) $_str .= ' ';
		}
		$n = strlen($_str);
		$str = '';
		for ($i = 1; $i <= $n; $i ++)
			$str .= (string) $_str [$n - $i];
		return $str;
	}
} 