<?php


namespace App\Service;


use App\Entity\Index\Attribute;
use App\Entity\Index\Competition;
use App\Entity\Index\Term;
use NXP\Stemmer;

class IndexService
{
    private static string        $stopWordsFilename = RESOURCES_PATH . 'search' . DIRECTORY_SEPARATOR . 'stopwords-ru.json';
    private static string        $indexFilename     = RESOURCES_PATH . 'search' . DIRECTORY_SEPARATOR . 'index.json';
    private static string        $matrixFilename    = RESOURCES_PATH . 'search' . DIRECTORY_SEPARATOR . 'matrix.json';

    private static ?array        $stopWords = null;


    /**
     * Return docId array
     *
     * @param string $query
     *
     * @return array
     */
    public static function search(string $query)
    {
        $result      = [];
        $queryVector = [];

        $terms = json_decode(DataLoadService::loadFromFile(self::$indexFilename), true);
        foreach (DataUtilService::explode(" ", $query) as $word) {
            $tempWord = IndexService::prepareWord($word);
            if (trim($tempWord) !== "") {
                foreach ($terms as $id => $term) {
                    if ($term['value'] === $tempWord) {
                        $queryVector[$id] = 1;
                        break;
                    }
                }
            }
        }

        $matrix            = json_decode(DataLoadService::loadFromFile(self::$matrixFilename), true);
        $vectorOnMatrixCos = [];
        foreach ($matrix as $docId => $docVector) {
            $vectorOnMatrixCos[$docId] = self::calculateCosineMeasureOfVectors($queryVector, $docVector);
        }

        if (count($vectorOnMatrixCos) !== 0) {
            foreach ($vectorOnMatrixCos as $docId => $cos) {
                if ($cos != 0) {
                    $result[] = $docId;
                }
            }
        }

        return $result;
    }


    /**
     * @param array $vectorA
     * @param array $vectorB
     *
     * @return float
     */
    public static function calculateCosineMeasureOfVectors(array $vectorA, array $vectorB)
    {
        $result = (float) 0;

        $keys    = array_merge(array_keys($vectorA), array_keys($vectorB));
        $aMultiB = (float) 0;
        $modA    = (float) 0;
        $modB    = (float) 0;
        foreach ($keys as $key) {
            if (isset($vectorA[$key])) {
                $modA += $vectorA[$key] * $vectorA[$key];
            }
            if (isset($vectorB[$key])) {
                $modB += $vectorB[$key] * $vectorB[$key];
            }
            if (isset($vectorA[$key]) && isset($vectorB[$key])) {
                $aMultiB += $vectorA[$key] * $vectorB[$key];
            }
        }

        if ($aMultiB !== (float) 0) {
            $result = $aMultiB / (sqrt($modA) * sqrt($modB));
        }

        return $result;
    }

    /**
     * @param array $competitions
     */
    public static function generateSearchMatrix(array $competitions)
    {
        $index = self::generateTermsIndex($competitions);
        DataSaveService::saveToFileAsJSON(self::$indexFilename, $index);

        $matrix = self::generateVectorMatrix($index);
        DataSaveService::saveToFileAsJSON(self::$matrixFilename, $matrix);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected static function generateTermsIndex(array $data)
    {
        $terms = [];

        $competitionWordsCount = [];

        $stemmedWords = array_unique(self::generateStemmedWordsArrayForIndexCalculate($data));
        natsort($stemmedWords);

        foreach ($stemmedWords as $stemmedWord) {
            $competitions = [];

            foreach ($data as $id => $value) {
                $count = substr_count(DataUtilService::stringToLower($value), $stemmedWord);
                if ($count !== 0) {
                    $competitions[] = new Competition(
                        new Attribute('id', $id),
                        new Attribute('count', $count)
                    );
                }

                if (isset($competitionWordsCount[$id])) {
                    $competitionWordsCount[$id] += $count;
                } else {
                    $competitionWordsCount[$id] = $count;
                }
            }

            $terms[] = new Term(new Attribute('value', $stemmedWord), $competitions);
        }

        $N = count($competitionWordsCount);

        foreach ($terms as $term) {
            if ($term instanceof Term) {
                $competitions = $term->getCompetitions();
                $tf           = log($N / count($competitions));

                foreach ($competitions as &$competition) {
                    if ($competition instanceof Competition) {
                        $idf    = $competition->getCount()->getValue() / $competitionWordsCount[$competition->getId()->getValue()];
                        $tf_idf = round($idf * $tf, 20);

                        $competition->setTfIdf(new Attribute('tf-idf', $tf_idf));
                    }
                }
            }
        }

        return $terms;
    }


    /**
     * @param array $termsIndex
     *
     * @return array
     */
    protected static function generateVectorMatrix(array $termsIndex = [])
    {
        $matrix = [];

        /** @var Term $termIndex */
        foreach ($termsIndex as $id => $termIndex) {
            /** @var Competition $competition */
            foreach ($termIndex->getCompetitions() as $competition) {
                $matrix[$competition->getId()->getValue()][$id] =
                    (float) $competition->getTfIdf()->getValue();
            }
        }

        ksort($matrix);

        return $matrix;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public static function generateStemmedWordsArrayForIndexCalculate(array $data)
    {
//    получение данных в виде строки
        $data = implode(' ', $data);

//    Очистка от символов
        $data = DataUtilService::pregReplace($data);
//    Нижний регистр
        $data = DataUtilService::stringToLower($data);
//    Стопслова
        foreach (self::getStopWords() as $stopWord) {
            $data = DataUtilService::pregReplace($data, "/\b" . $stopWord . "\b/u", " ");
        }
//    Лишние пробелы
        $data = DataUtilService::pregReplace($data, "/\s{2,}/", " ");
//    Лемматизизация
        $arrayData = DataUtilService::clearArray(
            DataUtilService::explode(" ", $data)
        );

        $stemmer = new Stemmer();
        $stemmed = [];
        foreach ($arrayData as $word) {
            $stemmedWord = $stemmer->getWordBase($word);
            if (strlen($stemmedWord) > 0) {
                $stemmed[] = $stemmedWord;
            }
        }

        return $stemmed;
    }


    /**
     * @param string $word
     *
     * @return string
     */
    public static function prepareWord(string $word)
    {
        $word = DataUtilService::pregReplace($word);

        $word = DataUtilService::stringToLower($word);

        if (self::$stopWords === null) {
            self::$stopWords = self::getStopWords();
        }
        foreach (self::$stopWords as $stop_word) {
            $word = DataUtilService::pregReplace($word, "/\b" . $stop_word . "\b/u", " ");
        }
//    Лишние пробелы
        $word = DataUtilService::pregReplace($word, "/\s{2,}/", " ");
//    Лемматизизация
        $stemmer = new Stemmer();
        $word    = $stemmer->getWordBase($word);

        if (is_string($word) && strlen($word) !== 0) {
            return $word;
        }

        return "";
    }

    /**
     * @param array $array
     * @param array $terms
     */
    protected static function modifyQueryArray(array &$array, array $terms)
    {
        foreach ($array as &$value) {
            $value['docs'] = [];
            foreach ($terms as $term) {
                if ($term['@attributes']['value'] === $value['word'] && isset($term['doc'])) {
                    if (count($term['doc']) === 1) {
                        $value['docs'][] = $term['doc']['@attributes']['id'];
                    } else {
                        foreach ($term['doc'] as $doc) {
                            $value['docs'][] = $doc['@attributes']['id'];
                        }
                    }
                }
            }
            $value['count'] = count($value['docs']);
        }

        if (count($array)) {
            uasort($array, function ($a, $b) {
                if ($a['count'] < $b['count']) {
                    return -1;

                } elseif ($a['count'] > $b['count']) {
                    return 1;
                }

                return 0;
            });
        }
    }

    /**
     * @return array
     */
    public static function getStopWords()
    {
        return json_decode(
            DataLoadService::loadFromFile(self::$stopWordsFilename)
        );
    }
}