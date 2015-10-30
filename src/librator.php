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

	public $filename;
	public $break = '<br />';

	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * Чтение и разбивка текста по страницам
	 * @param  int $limit Количество строк на страницу
	 * @return string текст разбитый по страницам
	 */
	public function read($limit)
	{
		$file = file($this->filename);
		$break = $this->getBreak();

		$page = $this->currentPage();
		$start = $page * $limit - $limit;

		if (isset($file[$start])) {
			for($i = $start; $i < $start + $limit; $i++) {
				echo $file[$i].$break;
			}

			$page = [];
			$page['limit'] = $limit;
			$page['total'] = count($file);
			$page['current'] = $this->currentPage();

			self::pagination($page);
		} else {
			echo 'Данной страницы не существует!';
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

			include_once('pagination.php');
		}
	}
}
