<?php
/**
 *  Класс текстовой библиотеки
 *  Читает и обрабатывает текст из файла по страницам
 */

class Librator {

	public $filename;
	public $break = '<br />';

	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * Чтение и разбивка текста по страницам
	 * @param  int $limit    Количество строк на страницу
	 * @return string        текст разбитый по страницам
	 */
	public function read($limit)
	{
		$file = file($this->filename);
		$break = $this->getBreak();

		$page = $this->currentPage();
		$start = $page * $limit;

		if (isset($file[$start])) {
			for($i = $start; $i < $start + $limit; $i++) {
				echo $file[$i].$break;
			}
			$this->pagination();
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

	public function currentPage()
	{
		return !empty($_GET['page']) ? abs(intval($_GET['page'])) : 0;
	}

	public function pagination()
	{
		$page = $this->currentPage();

		if ($page > 0) {
			echo '&laquo; <a href="?page='.($page - 1).'">Предыдущая страница</a>';
		}

		//if ($page * $limit < count(file($this->filename)))
		echo '<a href="?page='.($page + 1).'">Следующая страница</a> &raquo;';
	}

}


$librator = new Librator('library.txt');
$librator->setBreak('<br>');
$librator->read(20);
