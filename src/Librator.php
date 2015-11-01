<?php
/**
 *  Класс текстовой библиотеки
 *  Читает и обрабатывает текст из файла по страницам
 * @license Code and contributions have MIT License
 * @link    http://visavi.net
 * @author  Alexander Grigorev <visavi.net@mail.ru>
 * @version 1.0
 */

namespace Visavi;

class Librator {

	public $break = '<br />';
	public static $filename;
	private static $_file = null;

	public function __construct($filename)
	{
		self::$filename = $filename;
	}


	protected function explode($text, $line_length){
		$line_arr = array();
		$text_ar = explode(' ', $text);
		$string = $text_ar[0];
		$text_ar_so = count($text_ar);

		for($i=1;$i<$text_ar_so;$i++){
			$string_test = $string.' '.$text_ar[$i];

			if(mb_strlen($string_test) > $line_length) {
				$line_arr[] = $string;
				$string = $text_ar[$i];
			}
			else $string = $string_test;
		}
		$line_arr[] = $string;
		return $line_arr;
	}

	protected function explode2($text, $words){
/*		$array_words = [];
		$text = explode(' ', $text);
		$count_words = count($text);


		for ($i=0; $i<$count_words; $i++){

			if
		}

		$line_arr[] = $string;
		return $line_arr;*/
	}


	/**
	 * Получение данных файла
	 * @return array массив строк
	 */
	public static function file()
	{
		if (is_null(self::$_file)) {
			if (file_exists(self::$filename)) {
				self::$_file = file(self::$filename);
			} else {
				self::$_file = ['Файл не найден!'];
			}
		}

		return 	self::$_file;
	}

	/**
	 * Чтение и разбивка текста по страницам
	 * @param  int $limit Количество строк на страницу
	 * @return string текст разбитый по страницам
	 */
	public function read($limit, $separator = 'lines' /* words */)
	{
		$strings = [];
		$file = self::file();

		$break = $this->getBreak();

/*		$file = file_get_contents(self::$filename);
		$limit = 1;
		$file = $this->explode($file, 1000);*/



		$page = $this->currentPage();
		$start = $page * $limit - $limit;

		if (isset($file[$start])) {
			for($i = $start; $i < $start + $limit; $i++) {
				if (isset($file[$i])) {
					$strings[] = nl2br($file[$i], $break);
				}
			}

			$page = [];
			$page['limit'] = $limit;
			$page['total'] = count($file);
			$page['current'] = $this->currentPage();

			return implode($strings).self::pagination($page);
		} else {
			return 'Данной страницы не существует!';
		}
	}

	/**
	 * Установка разделителя строк
	 * @param string $break разделитель строк
	 */
	public function setBreak($break)
	{
		$this->break = $break;
	}

	/**
	 * Получить разделитель строк
	 * @return string разделитель строк
	 */
	public function getBreak()
	{
		return $this->break;
	}

	/**
	 * Получение название файла
	 */
	public function getTitle()
	{
		$file = self::$filename;

		return substr($file, 0, strrpos($file, '.'));;
	}

	/**
	 * Получение текущей страницы
	 * @return integer номер страницы
	 */
	public function currentPage()
	{
		return !empty($_GET['page']) ? abs(intval($_GET['page'])) : 1;
	}

	/**
	 * Постраничная навигация
	 * @param  array $page массив данных
	 * @return string  сформированный блок с кнопками страниц
	 */
	public static function pagination($page)
	{
		if ($page['total'] > 0) {
			if (empty($page['crumbs'])) $page['crumbs'] = 3;

			$pages = [];
			$pg_cnt = ceil($page['total'] / $page['limit']);
			$idx_fst = max($page['current'] - $page['crumbs'], 1);
			$idx_lst = min($page['current'] + $page['crumbs'], $pg_cnt);

			if ($page['current'] != 1) {
				$pages[] = [
					'page' => $page['current'] - 1,
					'title' => 'Предыдущая',
					'name' => '«',
				];
			}
			if ($page['current'] > $page['crumbs'] + 1) {
				$pages[] = [
					'page' => 1,
					'title' => '1 страница',
					'name' => 1,
				];
				if ($page['current'] != $page['crumbs'] + 2) {
					$pages[] = [
						'separator' => true,
						'name' => ' ... ',
					];
				}
			}
			for ($i = $idx_fst; $i <= $idx_lst; $i++) {
				if ($i == $page['current']) {
					$pages[] = [
						'current' => true,
						'name' => $i,
					];
				} else {
					$pages[] = [
						'page' => $i,
						'title' => $i.' страница',
						'name' => $i,
					];
				}
			}
			if ($page['current'] < $pg_cnt - $page['crumbs']) {
				if ($page['current'] != $pg_cnt - $page['crumbs'] - 1) {
					$pages[] = [
						'separator' => true,
						'name' => ' ... ',
					];
				}
				$pages[] = [
					'page' => $pg_cnt,
					'title' => $pg_cnt . ' страница',
					'name' => $pg_cnt,
				];
			}
			if ($page['current'] != $pg_cnt) {
				$pages[] = [
					'page' => $page['current'] + 1,
					'title' => 'Следующая',
					'name' => '»',
				];
			}

			return self::render('pagination', compact('pages'));
		}
	}

	/**
	 * Вывод шаблона
	 * @param  string $view   имя шаблона
	 * @param  array  $params массив переменных
	 * @return string         сформированный шаблон
	 */
	public static function render($view, $params = []){

		extract($params);
		ob_start();

		include ($view.'.php');

		return ob_get_clean();
	}
}
