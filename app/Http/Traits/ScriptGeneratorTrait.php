<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Response as FunctionType;

trait ScriptGeneratorTrait
{

    public function replaceWithAs($filterArray = [], $expression = "//", $content)
    {
        return preg_replace_callback($expression, function ($matches) use ($filterArray) {
            if (array_key_exists($matches[1], $filterArray)) {
                return $matches[1] . " AS " . $filterArray[$matches[1]];
            } else {
                return $matches[0];
            }
        }, $content);
    }
    public function replaceFilters($filterArray = [], $valueArray, $expression = "//", $content)
    {
        $filterCollection = collect($filterArray);
        return preg_replace_callback($expression, function ($matches) use ($filterCollection, $valueArray) {
            if ($replace = $filterCollection->where('key', $matches[1])->first()) {
                return $valueArray[$replace["key"]];
            } else {
                return $matches[0];
            }
        }, $content);
    }

    public function replaces($dataArray = [], $firstLevelExpression = "//", $secondLevelExpresion = "//", $content, $ln = false)
    {
        return preg_replace_callback($firstLevelExpression, function ($matches) use ($secondLevelExpresion, $dataArray, $ln) {
            $code = $matches[1];
            $valName = $matches[2];
            $result = "";
            if (array_key_exists($valName, $dataArray)) {
                foreach ($dataArray[$valName] as $valData) {
                    $result .= $this->matchReplaceCode($valData, $secondLevelExpresion, $code) . (($ln) ? "\n" : "");
                }
            } else {
                return $matches[0];
            }
            return $result;
        }, $content);
    }
}
