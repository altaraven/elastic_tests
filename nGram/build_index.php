<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/../vendor/autoload.php');

$indexName = 'mralbert_ngram_3';

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
                        'min_gram' => 2,
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
                    ],
                    'numberVariant' => [
                        'type' => 'string',
                    ],
                    'exerciseNumberVariant1' => [
                        'type' => 'string',
                    ],
                    'exerciseNumberVariant2' => [
                        'type' => 'string',
                    ],
                    'exerciseNumberVariant3' => [
                        'type' => 'string',
                    ],
                    'subChapterName' => [
                        'type' => 'string',
                    ],
                    'variant' => [
                        'type' => 'string',
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