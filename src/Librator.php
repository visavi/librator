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
	protected static $_file = null;

	public function __construct($filename)
	{
		self::$filename = $filename;
	}

	/**
	 * Чтение и разбивка текста по страницам
	 * @param  integer $limit     Количество строк на страницу
	 * @param  string  $separator Разделитель lines, words или chars
	 * @return string             Текст разбитый по страницам
	 */
	public function read($limit, $separator = 'lines')
	{
		$file = $this->prepareFile();
		$strings = [];

		if ($separator == 'words') {

			$file = $this->explodeWords($file, $limit);
			$limit = 1;

		} elseif ($separator == 'chars') {

			$file = $this->explodeChars($file, $limit);
			$limit = 1;

		} else {
			$file = explode("\n", $file);
		}

		$page = $this->currentPage();
		$start = $page * $limit - $limit;

		if (! isset($file[$start])) return 'Данной страницы не существует!';

		for($i = $start; $i < $start + $limit; $i++) {
			if (isset($file[$i])) {
				$strings[] = nl2br($file[$i]);
			}
		}

		$page = ['limit' => $limit, 'total' => count($file), 'current' => $this->currentPage()];

		return implode($strings).self::pagination($page);
	}

	/**
	 * Получение название файла
	 */
	public function getTitle()
	{
		return current(explode("\n", self::file()));
	}

	/**
	 * Получение текущей страницы
	 * @return integer номер страницы
	 */
	public function currentPage()
	{
		return ! empty($_GET['page']) ? abs(intval($_GET['page'])) : 1;
	}

	/**
	 * Получение данных файла
	 * @return array массив строк
	 */
	protected static function file()
	{
		if (is_null(self::$_file)) {
			if (file_exists(self::$filename)) {
				self::$_file = file_get_contents(self::$filename);
			}
		}

		return 	self::$_file;
	}

	/**
	 * Постраничная навигация
	 * @param  array $page Массив данных
	 * @return string      Сформированный блок с кнопками страниц
	 */
	protected static function pagination($page)
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
	 * Проверка и подготовка файла, удаление заголовка
	 * @return string текст
	 */
	protected static function prepareFile()
	{
		$file = self::file();

		if (! $file) return 'Файл не найден!';

		$lines = explode("\n", $file);
		if (isset($lines[0]) && isset($lines[1])) {
			unset($lines[0]);
			$file = implode("\n", $lines);
		}

		return $file;
	}

	/**
	 * Разбивает файл на массив с учетом кол. символов
	 * @param  string  $text  текст
	 * @param  integer $chars кол. символов
	 * @return array          массив строк
	 */
	protected function explodeChars($text, $chars)
	{
		$lines = [];
		$string = '';
		$text = explode(' ', $text);
		$count_chars = count($text);

		for ($i = 0; $i < $count_chars; $i++){
			$string .= $text[$i].' ';

			if (mb_strlen($string) > $chars) {
				$lines[] = $string;
				$string = '';
			}
		}
		return $lines;
	}

	/**
	 * Разбивает файл на массив с учетом кол. слов
	 * @param  string  $text  текст
	 * @param  integer $words кол. слов
	 * @return array          массив строк
	 */
	protected function explodeWords($text, $words)
	{
		$lines = [];
		$array_words = [];
		$text = explode(' ', $text);
		$count_words = count($text);

		for ($i = 0; $i < $count_words; $i++){
			$array_words[] = $text[$i];

			if (count($array_words) > $words) {
				$lines[] = implode(' ', $array_words);
				$array_words = [];
			}
		}
		return $lines;
	}

	/**
	 * Вывод шаблона
	 * @param  string $view   Имя шаблона
	 * @param  array  $params Массив переменных
	 * @return string         Сформированный шаблон
	 */
	protected static function render($view, $params = [])
	{
		extract($params);
		ob_start();

		include ($view.'.php');

		return ob_get_clean();
	}
}
