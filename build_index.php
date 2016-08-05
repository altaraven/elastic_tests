<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/vendor/autoload.php');

$indexName = 'mralbert_swedish';
$typeName = 'exercises';

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
                    'swedish_stop' => [
                        'type' => 'stop',
                        'stopwords' => '_swedish_'
                    ],
//                    'swedish_keywords' => [
//                        'type' => 'keyword_marker',
//                        'keywords' => []
//                    ],
                    'swedish_stemmer' => [
                        'type' => 'stemmer',
                        'language' => 'swedish'
                    ]
                ],
                'analyzer' => [
                    'swedish' => [
                        'tokenizer' => 'standard',
                        'filter' => [
                            'lowercase',
                            'swedish_stop',
//                            'swedish_keywords',
                            'swedish_stemmer'
                        ]
                    ]
                ]
            ],
        ],
        'mappings' => [
            $typeName => [
                'properties' => [
                    'chapterNumber' => [
                        'type' => 'string'
                    ],
                    'chapterName' => [
                        'type' => 'string'
                    ],
                    'exerciseText' => [
                        'type' => 'string'
                    ],
                    'lessonId' => [
                        'type' => 'string'
                    ],
                    'lessonName' => [
                        'type' => 'string'
                    ],
                    'number' => [
                        'type' => 'long',
                        'boost' => 2
                    ],
                    'numberVariant' => [
                        'type' => 'string',
                        'boost' => 3
                    ],
                    'exerciseNumberVariant1' => [
                        'type' => 'string',
                        'boost' => 2
                    ],
                    'exerciseNumberVariant2' => [
                        'type' => 'string',
                        'boost' => 2
                    ],
                    'exerciseNumberVariant3' => [
                        'type' => 'string',
                        'boost' => 2
                    ],
                    'subChapterName' => [
                        'type' => 'string'
                    ],
                    'variant' => [
                        'type' => 'string',
                        'boost' => 2
                    ]
                ]
            ]
        ]
    ]
];


// Create the index with mappings and settings now
$response = $client->indices()->create($params);
var_dump($response);