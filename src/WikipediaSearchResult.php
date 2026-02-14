<?php

namespace JordJD\WikipediaSearch;

use JordJD\BaseSearch\Interfaces\SearchResultInterface;

class WikipediaSearchResult implements SearchResultInterface
{
    private $title;
    private $url;
    private $description;
    private $score;

    public function __construct(array $item, string $language, float $score)
    {
        $this->title = html_entity_decode($item['title'], ENT_QUOTES | ENT_HTML5);
        $this->url = 'https://'.$language.'.wikipedia.org/wiki/'.str_replace(' ', '_', $this->title);
        $this->description = html_entity_decode(strip_tags($item['snippet']).'...', ENT_QUOTES | ENT_HTML5);
        $this->score = $score;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getScore(): float
    {
        return $this->score;
    }
}