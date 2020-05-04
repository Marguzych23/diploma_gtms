<?php


namespace App\Service;


use NumPHP\Core\NumArray;
use NumPHP\LinAlg\Exception\SingularMatrixException;
use NumPHP\LinAlg\LinAlg;

class MatrixService
{

    /**
     * @param array $matrix
     * @param int   $n
     * @param int   $m
     *
     * @return array
     */
    public static function crop(array $matrix, int $n, int $m = null)
    {
        $new_matrix = [];
        $m          = $m ?? $n;

        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $new_matrix[$i][$j] = $matrix[$i][$j];
            }
        }

        return $new_matrix;
    }

    /**
     * @param array $matrix
     *
     * @return mixed
     * @throws SingularMatrixException
     */
    public static function invert(array $matrix)
    {
        return LinAlg::inv(new NumArray($matrix))->getData();
    }

    /**
     * @param array $matrix1
     * @param array $matrix2
     *
     * @return mixed
     */
    public static function multiply(array $matrix1, array $matrix2)
    {
        return (new NumArray($matrix1))->dot(new NumArray($matrix2))->getData();
    }

    /**
     * @param array $matrix
     *
     * @return array
     */
    public static function grad90(array $matrix)
    {
        $new_matrix = [];

        $n = count($matrix);
        if (is_array($matrix[$n - 1])) {
            $m = count($matrix[$n - 1] ?? []);
        } else {
            $m = 1;
        }

        for ($i = 0; $i < $m; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $new_matrix[$i][$j] = $matrix[$j][$i];
            }
        }

        return $new_matrix;
    }

    /**
     * @param array    $vector
     * @param int|null $n
     *
     * @return array
     */
    public static function createDiagMatrixByVector(array $vector, int $n = null)
    {
        $matrix = [];
        $n      = $n ?? count($vector);

        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                if ($i === $j) {
                    $matrix[$i][$j] = $vector[$i];
                } else {
                    $matrix[$i][$j] = (float) 0;
                }
            }
        }

        return $matrix;
    }


    /**
     * @param array $vector
     * @param array $matrix
     *
     * @return array
     */
    public static function multiply1(array $vector, array $matrix)
    {
        $res_vector = [];

        $i = 0;
        while ($i !== count($matrix[count($matrix) - 1])) {
            $res_vector[$i] = (float) 0;
            $i++;
        }

        for ($i = 0; $i < count($res_vector); $i++) {
            for ($j = 0; $j < count($vector); $j++) {
                $res_vector[$i] += $vector[$j] * $matrix[$j][$i];
            }
        }

        return $res_vector;
    }
}