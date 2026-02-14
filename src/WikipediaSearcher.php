<?php

namespace DivineOmega\WikipediaSearch;

use DivineOmega\BaseSearch\Interfaces\SearcherInterface;

class WikipediaSearcher implements SearcherInterface
{
    const URL = 'https://[LANGUAGE].wikipedia.org/w/api.php?action=query&list=search&utf8=&format=json&srlimit=500&srsearch=[QUERY]';
    private const USER_AGENT = 'jord-jd-wikipedia-search/1.0 (+https://github.com/Jord-JD/php-wikipedia-search)';

    private $language;

    public function __construct(string $language)
    {
        $this->language = $language;
    }

    public function search(string $query): array
    {
        $url = $this->buildUrl($query);

        $response = $this->fetch($url);
        $decodedResponse = json_decode($response, true);
        if (!is_array($decodedResponse) || !isset($decodedResponse['query']['search']) || !is_array($decodedResponse['query']['search'])) {
            return [];
        }

        $results = [];

        $count = count($decodedResponse['query']['search']);
        if ($count === 0) {
            return [];
        }

        foreach ($decodedResponse['query']['search'] as $index => $item) {
            $score = ($count - $index) / $count;
            $results[] = new WikipediaSearchResult($item, $this->language, $score);
        }

        return $results;
    }

    private function fetch(string $url): string
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 5,
                'header' => "User-Agent: " . self::USER_AGENT . "\r\n",
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            throw new \RuntimeException('Unable to fetch Wikipedia search results.');
        }

        return $response;
    }

    private function buildUrl(string $query): string
    {
        return str_replace(
            ['[QUERY]', '[LANGUAGE]'],
            [urlencode($query), urlencode($this->language)],
            self::URL
        );
    }
}
