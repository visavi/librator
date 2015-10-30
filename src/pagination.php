<nav>
	<ul class="pagination">
		<?php foreach($pages as $page): ?>
			<?php if(isset($page['separator'])): ?>
				<li><span><?= $page['name'] ?></span></li>
			<?php elseif(isset($page['current'])): ?>
				<li class="active"><span data-toggle="tooltip" title="Текущая"><?= $page['name'] ?></span></li>
			<?php else: ?>
				<li><a href="?page=<?= $page['page'] ?>" data-toggle="tooltip" title="<?= $page['title'] ?>"><?= $page['name'] ?></a></li>
			<?php endif; ?>
		<?php endforeach; ?>
	</ul>
</nav>
