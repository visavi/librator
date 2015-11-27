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
		$file = $this->prepareFile($limit, $separator);

		$page = $this->currentPage();

		$string = $file[$page - 1];

		$page = ['limit' => 1, 'total' => count($file), 'current' => $this->currentPage()];

		return nl2br($string).self::pagination($page);
	}

	/**
	 * Получение заголовка из 1 строки
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
	protected function prepareFile($limit, $separator)
	{
		$file = self::file();

		if (! $file) return 'Файл не найден!';

		$file = explode("\n", $file);
		if (isset($file[0]) && isset($file[1])) {
			unset($file[0]);
		}
		$file = implode("\n", $file);

		$file = $this->explode($file, $limit, $separator);

		return $file;
	}

	/**
	 * Разбивает файл на массив с учетом разделителя
	 * @param  string  $text      текст
	 * @param  integer $limit     кол. элементов
	 * @param  string  $separator разделитель
	 * @return array              массив строк
	 */
	public function explode($text, $limit, $separator)
	{
		$lines = [];
		$array = [];
		$string = '';

		$text = explode($separator == 'lines' ? "\n" : ' ', $text);
		$count = count($text);

		for ($i = 0; $i < $count; $i++){

			if ($separator == 'chars') {
				$string .= $text[$i].' ';

				if (mb_strlen($string) > $limit) {
					$lines[] = $string;
					$string = '';
				}
			} else {
				$array[] = $text[$i];

				if (count($array) > $limit) {
					$lines[] = implode(' ', $array);
					$array = [];
				}
			}
		}

		if (! empty($array)) {
			$lines[] = implode(' ', $array);
		}

		if (! empty($string)) {
			$lines[] = $string;
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
