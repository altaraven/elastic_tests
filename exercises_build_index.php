<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/vendor/autoload.php');

$indexName = 'mralbert_swedish_full_8';
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
                'analyzer' => [
                    'my_analyzer' => [
                        'type' => 'snowball',
                        'language' => 'Swedish'
                    ]
                ]
            ],
//            'analysis' => [
//                'filter' => [
//                    'swedish_stop' => [
//                        'type' => 'stop',
//                        'stopwords' => '_swedish_'
//                    ],
////                    'swedish_keywords' => [
////                        'type' => 'keyword_marker',
////                        'keywords' => []
////                    ],
//                    'swedish_stemmer' => [
//                        'type' => 'stemmer',
//                        'language' => 'swedish'
//                    ]
//                ],
//                'analyzer' => [
//                    'swedish' => [
//                        'tokenizer' => 'standard',
//                        'filter' => [
//                            'lowercase',
//                            'swedish_stop',
////                            'swedish_keywords',
//                            'swedish_stemmer'
//                        ]
//                    ]
//                ]
//            ],
        ],
        'mappings' => [
            $typeName => [
                'properties' => [
                    'chapterNumber' => [
                        'type' => 'string'
                    ],
                    'chapterName' => [
                        'type' => 'string',
                        'analyzer' => 'my_analyzer'
                    ],
                    'exerciseText' => [
                        'type' => 'string',
//                        'index' => 'analyzed'
                        'analyzer' => 'my_analyzer'
                    ],
                    'lessonId' => [
                        'type' => 'string',
                        'include_in_all' => false,
                    ],
                    'lessonName' => [
                        'type' => 'string',
                        'include_in_all' => false,
                    ],
                    'number' => [
                        'type' => 'long',
//                        'boost' => 2
                    ],
                    'numberVariant' => [
                        'type' => 'string',
//                        'boost' => 3
                    ],
                    'exerciseNumberVariant1' => [
                        'type' => 'string',
//                        'boost' => 2
                    ],
                    'exerciseNumberVariant2' => [
                        'type' => 'string',
//                        'boost' => 2
                    ],
                    'exerciseNumberVariant3' => [
                        'type' => 'string',
//                        'boost' => 2
                    ],
                    'subChapterName' => [
                        'type' => 'string',
//                        'index' => 'analyzed'
                        'analyzer' => 'my_analyzer'
                    ],
                    'variant' => [
                        'type' => 'string',
//                        'boost' => 2
                    ],
                    'numVarChaptSubChapt' => [
                        'type' => 'string'
                    ],
                    'suggest' => [
                        'type' => 'completion',
                        'analyzer' => 'simple',
                        'search_analyzer' => 'simple',
                        'payloads' => false
                    ]
                ]
            ]
        ]
    ]
];


// Create the index with mappings and settings now
$response = $client->indices()->create($params);
var_dump($response);