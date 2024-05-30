<?php

namespace App\Sources;

use App\Sources\Amazon\AmazonSource;

class Sources
{
    protected static $sources = [
        //OpenLibrarySource::class,
        //AmazonSource::class,
        DnbSource::class,
        LocSource::class,
    ];


    public static function query($isbn)
    {
        $result = [];
        foreach (self::$sources as $sourceClass) {
            /** @var AbstractSource $source */
            $source = new $sourceClass();
            $sourceResult = $source->query($isbn);
            if (count($sourceResult)) {
                $result[] =[
                    'source' => $source->getSourceTitle(),
                    'data' => $sourceResult
                ];
            }
        }
        return $result;
    }

}
