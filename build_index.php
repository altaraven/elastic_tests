<?php
date_default_timezone_set('Europe/Kiev');

require(__DIR__ . '/vendor/autoload.php');

$indexName = 'mralbert_final_8';

$clientBuilder = Elasticsearch\ClientBuilder::create();
$clientBuilder->setHosts([
    'http://localhost:9200/'
]);
$client = $clientBuilder->build();

$params = [
    'index' => $indexName,
    'body' => [
//        'settings' => [
//            'analysis' => [
//                'analyzer' => [
//                    'my_analyzer' => [
//                        'type' => 'snowball',
//                        'language' => 'Swedish'
//                    ]
//                ]
//            ],
//        ],
        'mappings' => [
//            'lessons' => [
//                'properties' => [
//                    'lessonId' => [
//                        'type' => 'string',
//                        'include_in_all' => false,
//                    ],
//                    'lessonName' => [
//                        'type' => 'string',
//                    ],
//                    'chapterName' => [
//                        'type' => 'string',
//                    ],
//                    'subChapterName' => [
//                        'type' => 'string',
//                    ],
//                    'lessons_suggest' => [
//                        'type' => 'completion',
//                        'analyzer' => 'simple',
//                        'search_analyzer' => 'simple',
//                        'payloads' => false
//                    ],
//                ],
//            ],
            'exercises_suggest' => [
                'properties' => [
                    'word' => [
                        'type' => 'string',
                    ],
                    'exercises_suggest' => [
                        'type' => 'completion',
                        'analyzer' => 'simple',
                        'search_analyzer' => 'simple',
                        'payloads' => false
                    ]
                ],
            ],
            'exercises' => [
                'properties' => [
                    'chapterNumber' => [
                        'type' => 'string'
                    ],
                    'chapterName' => [
                        'type' => 'string',
//                        'analyzer' => 'my_analyzer'
                    ],
                    'exerciseText' => [
                        'type' => 'string',
//                        'analyzer' => 'my_analyzer'
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
//                        'analyzer' => 'my_analyzer'
                    ],
                    'variant' => [
                        'type' => 'string',
//                        'boost' => 2
                    ],
                    'numVarChaptSubChapt' => [
                        'type' => 'string'
                    ],
//                    'suggest' => [
//                        'type' => 'completion',
//                        'analyzer' => 'simple',
//                        'search_analyzer' => 'simple',
//                        'payloads' => false
//                    ]
                ]
            ]
        ]
    ]
];


// Create the index with mappings and settings now
$response = $client->indices()->create($params);
var_dump($response);