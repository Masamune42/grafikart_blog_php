<?php

namespace App\Model;

use App\Helpers\Text;
use DateTime;

class Post
{
    private $id;

    private $name;

    private $slug;

    private $content;

    private $created_at;

    private $categories = [];

    public function getExcerpt(): ?string
    {
        if ($this->content === null)
            return null;
        return nl2br(htmlentities(Text::excerpt($this->content, 60)));
    }

    /**
     * Get the value of id
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get the value of name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the value of created_at
     */
    public function getCreated_at(): \DateTime
    {
        return new DateTime($this->created_at);
    }

    /**
     * Get the value of categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Get the value of slug
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * Set the value of slug
     *
     * @return  self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }
}
