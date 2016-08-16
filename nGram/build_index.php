<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/../vendor/autoload.php');

$indexName = 'mralbert_ngram_10';

$clientBuilder = Elasticsearch\ClientBuilder::create();
$clientBuilder->setHosts([
    'http://localhost:9200/'
]);
$client = $clientBuilder->build();

$params = [
    'index' => $indexName,
    'body' => [
        'settings' => [
            'analysis' => [
                'filter' => [
                    'nGram_filter' => [
                        'type' => 'nGram',
                        'min_gram' => 1,
                        'max_gram' => 20,
                        'token_chars' => [
                            'letter',
                            'digit',
                            'punctuation',
                            'symbol'
                        ]
                    ]
                ],
                'analyzer' => [
                    'nGram_analyzer' => [
                        'type' => 'custom',
                        'tokenizer' => 'whitespace',
                        'filter' => [
                            'lowercase',
                            'asciifolding',
                            'nGram_filter'
                        ]
                    ],
                    'whitespace_analyzer' => [
                        'type' => 'custom',
                        'tokenizer' => 'whitespace',
                        'filter' => [
                            'lowercase',
                            'asciifolding'
                        ]
                    ]
                ]
            ]
        ],
        'mappings' => [
            'lessons' => [
                '_all' => [
                    'analyzer' => 'nGram_analyzer',
                    'search_analyzer' => 'whitespace_analyzer',
//                    'store' => true,
                ],
                'properties' => [
                    'id' => [
                        'type' => 'string',
                        'index' => 'no',
                        'include_in_all' => false,
                    ],
                    'name' => [
                        'type' => 'string',
                    ],
                    'keywords' => [
                        'type' => 'string',
                    ],
                    'centralArea' => [
                        'type' => 'string',
                    ],
                    'mainArea' => [
                        'type' => 'string',
                    ],
                ],
            ],
            'exercises' => [
                '_all' => [
                    'analyzer' => 'nGram_analyzer',
                    'search_analyzer' => 'whitespace_analyzer',
//                    'store' => true,
                ],
                'properties' => [
                    'isbn' => [
                        'type' => 'string',
                        'index' => 'no',
                        'include_in_all' => false,
                    ],
                    'chapterNumber' => [
                        'type' => 'string'
                    ],
                    'chapterName' => [
                        'type' => 'string',
                    ],
                    'exerciseText' => [
                        'type' => 'string',
                    ],
                    'lessonId' => [
                        'type' => 'string',
                        'index' => 'no',
                        'include_in_all' => false,
                    ],
                    'lessonName' => [
                        'type' => 'string',
                        'index' => 'no',
                        'include_in_all' => false,
                    ],
                    'number' => [
                        'type' => 'long',
//                        'boost' => 2,
                    ],
                    'numberVariant' => [
                        'type' => 'string',
//                        'boost' => 10,
                    ],
                    'exerciseNumberVariant1' => [
                        'type' => 'string',
//                        'boost' => 2,
                    ],
                    'exerciseNumberVariant2' => [
                        'type' => 'string',
//                        'boost' => 2,
                    ],
                    'exerciseNumberVariant3' => [
                        'type' => 'string',
//                        'boost' => 2,
                    ],
                    'subChapterName' => [
                        'type' => 'string',
                    ],
                    'variant' => [
                        'type' => 'string',
//                        'boost' => 2,
                    ],
                    'numVarChaptSubChapt' => [
                        'type' => 'string'
                    ],
                ]
            ]
        ]
    ]
];


// Create the index with mappings and settings now
$response = $client->indices()->create($params);
var_dump($response);