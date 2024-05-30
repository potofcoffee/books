<?php

namespace App\Sources;

use App\Models\Author;
use App\Sources\Schemas\QueryResponse;
use Illuminate\Support\Str;
use Scriptotek\Marc\Collection;
use Scriptotek\Sru\Client;
use Scriptotek\Sru\Record;

abstract class AbstractSruSource extends AbstractSource
{
    protected $url = '';
    protected $version = '1.1';
    protected $schema = 'marcxml';
    protected $searchRetrieveOperation = 'searchRetrieve';

    protected $filter = [
        'isbn' => ['020', 'a'],
        'title' => [245, 'a'],
        'subtitle' => [245, 'b'],
        'place' => [264, 'a',null,'/'],
        'publisher' => [264, 'b',null,'/'],
        'year' => [264, 'c',null, ','],
        'ddc' => ['082', 'a'],
    ];

    protected $client = null;
    protected $guzzle = null;

    public function __construct()
    {
        $this->guzzle = new \GuzzleHttp\Client();

        $this->client = new Client($this->url, [
            'schema' => $this->schema,
            'version' => $this->version,
            'user-agent' => config('app.name'),
        ],
            $this->guzzle);
    }

    protected function queryString($isbn) {
        return 'isbn='.$isbn;
    }

    public function query($isbn): array
    {
        $result = [];
        /** @var Record $record */
        foreach ($this->client->records($this->queryString($isbn), 10, [], $this->guzzle) as $record) {
            $record = QueryResponse::fromSruRecord($isbn, $record, $this->filter, [$this, 'getAuthors'], [$this, 'stringFilter']);
            $record['book']['title'] = preg_replace('/[\x98\x9c]/u', '', $record['book']['title']);
            $record['book']['year'] = trim($record['book']['year'], '[] ');
            $result[] = $record;
        };
        return $result;

    }

    public function getAuthors(Record $record): array {
        $raw = QueryResponse::getDataField($record, 100, 'a');
        if ($raw) $raw = trim($raw, " \t\n\r\0\x0B,");
        return $raw ? [Author::normalizeName($raw)['name']] : [];
    }

    public function stringFilter($s) {
        return $s;
    }

}
