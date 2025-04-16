<?php
namespace App\Presentation\Home;

use App\Model\PostFacade;
use Nette;

final class HomePresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private PostFacade $facade,
	) {
	}

	public function renderDefault(int $page = 1): void
	{
		$postsCount = $this->facade->getPostsCount();

		$paginator = new Nette\Utils\Paginator;
		$paginator->setItemCount(itemCount: $postsCount); // celkový počet článků
		$paginator->setItemsPerPage(itemsPerPage: 5); // počet položek na stránce
		$paginator->setPage($page);

		$this->template->posts = $this->facade
			->getPublicArticles($paginator->getLength(), $paginator->getOffset());

		// $this->template->categories = $this->facade->getCategories();
		$this->template->paginator = $paginator;
	}

	// public function renderCategory(int $id): void {
	// 	$category = $this->facade->getCategoryById($id);
	// 	if (!$category) {
	// 		$this->error('Category not found');
	// 	}

	// 	$this->template->category = $category; 
	// }
}
