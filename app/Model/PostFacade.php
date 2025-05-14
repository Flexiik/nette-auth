<?php
namespace App\Model;

use Nette;

final class PostFacade
{
    public function __construct(
        private Nette\Database\Explorer $database,
    ) {
    }

    // public function getPublicArticles()
    // {
    //     return $this->database
    //         ->table('posts')
    //         // ->where('created_at <= ', new \DateTime)
    //         ->order('created_at DESC');
    // }

    public function updateRating(int $userId, int $postId, ?int $liked): void
    {
        $row = $this->database->table('rating')
            ->where('user_id', $userId)
            ->where('post_id', $postId)
            ->fetch();

        if ($row) {
            // Pokud řádek existuje, udělej update
            $row->update([
                'liked' => $liked,
            ]);
        } else {
            // Pokud neexistuje, udělej insert
            $this->database->table('rating')->insert([
                'user_id' => $userId,
                'post_id' => $postId,
                'liked' => $liked,
            ]);
        }
    }

    public function getPublicArticles(int $limit, int $offset): Nette\Database\ResultSet
    {
        return $this->database->query('
        SELECT 
            posts.*,
            (SELECT COUNT(*) FROM rating WHERE rating.post_id = posts.id AND liked = 1) AS likes,
            (SELECT COUNT(*) FROM rating WHERE rating.post_id = posts.id AND liked = 0) AS dislikes
        FROM posts
        WHERE created_at < ?
        ORDER BY created_at DESC
        LIMIT ?
        OFFSET ?',
            new \DateTime,
            $limit,
            $offset,
        );
    }

    public function getPostById(int $postId) {
        return $this->database
            ->table('posts')
            ->get($postId);
    }

    public function updatePost(int $postId, array $data) {
        $post = $this->getPostById($postId);
        $post->update($data);
        return $post;
    }

    public function addView(int $postId) {
        $post = $this->getPostById($postId);
        $post->update(["views_count" => $post->views_count + 1]);
        return $post;
    }

    public function getPostsCount(): int
    {
        return $this->database->fetchField('SELECT COUNT(*) FROM posts WHERE created_at < ?', new \DateTime);
    }

    // public function getPostCategory(int $postId) {
    //     $post = $this->getPostById($postId);
    //     return $post->category;
    // }

    public function deleteComment($commentId) {
        $comment = $this->database->table('comments')->get($commentId)->delete();
    }

    public function createPost(array $data) {
        $post = $this->database
            ->table('posts')
            ->insert($data);
        return $post;
    }

    public function getComments(int $postId) {
        return $this->getPostById($postId)->related('comments')->order('created_at');
    }

    public function getCategories() {
        return $this->database->table('category');
    }

    public function getCategoryById(int $id) {
        return $this->getCategories()->get($id);
    }

    public function getCategorySelectValues() {
        return $this->getCategories();
    }
}
