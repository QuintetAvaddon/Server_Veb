<?php
namespace MyProject\Models;

class Article extends ActiveRecordEntity
{
    protected $name;
    protected $text;
    protected $author_id;
    protected $created_at;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getAuthorId(): int
    {
        return $this->author_id;
    }

    public function setAuthorId(int $authorId): void
    {
        $this->author_id = $authorId;
    }

    public function getAuthor(): User
    {
        return User::getById($this->author_id);
    }

    protected static function getTableName(): string
    {
        return 'articles';
    }
    public function getCreatedAt(): string
    {
    return $this->created_at;
    }
}
