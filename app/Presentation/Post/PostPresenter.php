<?php
namespace App\Presentation\Post;

use Nette;
use Nette\Application\UI\Form;
use App\Model\PostFacade;

final class PostPresenter extends Nette\Application\UI\Presenter
{
	public function __construct(
		private PostFacade $facade,
	) {
	}

    public function renderShow(int $id): void {
        $post = $this->facade->addView($id);
        if (!$post) {
            $this->error('Stránka nebyla nalezena');
        }

		if($post->status == "ARCHIVED" && !$this->user->isLoggedIn()) {
			$this->flashMessage("K tomuto obsahu nemáte přístup.");
			$this->redirect(destination: "Home:default");
		}

        $this->template->post = $post;
    }

	public function handleLiked(int $postId, int $liked) {
     // pokud je uživatel přihlášen budete volat PostFacade metodu updateRating
		$this->facade->updateRating($this->getUser()->getId(), $postId, $liked);
	}
}
