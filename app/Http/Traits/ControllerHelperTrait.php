<?php

namespace App\Http\Traits;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\RelationNotFoundException;
use Illuminate\Support\Facades\Storage;

trait ControllerHelperTrait
{

	/**
	 * Permet d'ajouter des filtres sur un objet Eloquent
	 * @param 	mixed 	$query				L'objet Eloquent
	 * @param 	mixed 	$requestData		Les données de la requete
	 * @param 	mixed 	$modelName			Le nom du model
	 * @return 	mixed
	 */
	public function queryFilter($query, $requestData, $modelName)
	{
		$modelPath = "\App\Models\\$modelName";
		foreach ($requestData as $filter => $value) {
			if (in_array($filter, Schema::getColumnListing((new $modelPath)->getTable())) && $requestData[$filter]) {
				if (in_array($requestData[$filter], ["true", "false"])) {
					$requestData[$filter] = ($requestData[$filter] == "true") ? 1 : 0;
				}
				$query->where($filter, $requestData[$filter]);
			}
		}
		return $query;
	}

	/**
	 * Permet d'ajouter des filtres json sur un objet Eloquent
	 * @param 	mixed 	$query				L'objet Eloquent
	 * @param 	mixed 	$jsonFilter			Les données de la requete
	 * @param 	mixed 	$modelName			Le nom du model
	 * @param 	mixed 	$prefix				Le prefix pour reconnaître le filtre
	 * @return 	mixed
	 */
	public function queryJSONFilter($query, $jsonFilter, $modelName, $prefix = "json_with_")
	{
		$jsonFilterList = collect($jsonFilter)->filter(function ($value, $key) use ($prefix) {
			return strpos($key, $prefix) === 0;
		});

		$modelPath = "\App\Models\\$modelName";
		foreach ($jsonFilterList as $jsonFilter => $value) {
			$jsonFilter = substr($jsonFilter, strlen($prefix));
			if (in_array($jsonFilter, Schema::getColumnListing((new $modelPath)->getTable())) && $jsonFilter[$jsonFilter]) {
				if (in_array($jsonFilter[$jsonFilter], ["true", "false"])) {
					$jsonFilter[$jsonFilter] = ($jsonFilter[$jsonFilter] == "true") ? 1 : 0;
				}
				$query->where($jsonFilter, $jsonFilter[$jsonFilter]);
			}
		}
		return $query;
	}

	// /**
	//  * Permet d'ajouter le filtre au bon niveau
	//  * @param mixed $query					Le query builder
	//  * @param $filterCollection				La collection des relations à suivre
	//  * @param mixed $values					La valeur à filtrer
	//  * @return Builder
	//  */
	// public function recurciveFilter(Builder $query, Collection $filterCollection, $values): Builder
	// {
	// 	$filter_level = $filterCollection->shift();
	// 	if ($filterCollection->isEmpty()) {
	// 		$valueList = explode('-', $values);
	// 		$query->where($filter_level, $valueList);
	// 	} else {
	// 		$callback = function ($query) use ($filterCollection, $values) {
	// 			return $this->recurciveFilter($query, $filterCollection, $values);
	// 		};
	// 		$query->whereHas($filter_level, $callback);
	// 	}
	// 	return $query;
	// }

	/**
	 * Permet d'ajouter des filtres sur les relation d'un objet eloquent par inclusion
	 * @param 	mixed 	$query				L'objet Eloquent
	 * @param 	mixed 	$requestData		Les données de la requete
	 * @param 	mixed 	$modelName			Le nom du model
	 * @return 	mixed
	 */
	public function queryRelationFilter($query, $requestData, $prefix = "relation_filter_in", $filterMethod = "in")
	{
		$relationData = collect($requestData)->filter(function ($value, $key) use ($prefix) {
			return strpos($key, $prefix) === 0;
		});
		foreach ($relationData as $filter => $value) {
			if (in_array($value, ["true", "false"])) {
				$relationData[$filter] = ($value == "true") ? 1 : 0;
			}

			$filterData = str_replace(">", ".", substr($filter, strlen($prefix)));
			$filterData = explode('-', $filterData);
			$relation = $filterData[0];
			$filter = $filterData[1];
			$valueArray = array_filter(explode('-', $value));
			if($valueArray){
				if ($filterMethod == "in") {
					$query->whereHas($relation, function ($query) use ($filter, $valueArray, $filterMethod) {
						$query->whereIn($filter, $valueArray);
					});
				} else {
					$query->whereDoesntHave($relation, function ($query) use ($filter, $valueArray, $filterMethod) {
						$query->whereIn($filter, $valueArray);
					});
				}
			}
			// $query = $this->recurciveFilter($query, collect($queryArray), $value);
		}
		return $query;
	}

	/**
	 * Permet d'ajouter des relation sur un objet Eloquent
	 * @param 	mixed 	$query				L'objet Eloquent
	 * @param 	mixed 	$requestData		Les données de la requete
	 * @param 	mixed 	$modelName			Le nom du model
	 * @return 	mixed
	 */
	public function queryRelationAdd($query, $requestData, $modelName)
	{
		$modelPath = "App\Models\\$modelName";
		$currentObjet = new $modelPath();
		$relations = collect($requestData)->filter(function ($value, $key) {
			return strpos($key, 'with_') === 0;
		});
		$validatedRelations = [];
		foreach ($relations as $relation => $value) {
			$relation = str_replace("<", ".", substr($relation, 5));
			try {
				$currentObjet->load($relation);
				($value == "true") ? $validatedRelations[] = $relation : null;
			} catch (RelationNotFoundException $ex) {
				continue;
			}
		}
		$query->with($validatedRelations);
		return $query;
	}

	/**
	 * Permet d'ajouter un filtre de recherche sur un objet Eloquent
	 * @param 	mixed 	$query				L'objet Eloquent
	 * @param 	mixed 	$columns			Les colones où rechercher
	 * @param 	mixed 	$search				Le mot clé à rechercher
	 * @return 	mixed
	 */
	public function querySearch($query, $columns, $search)
	{
		$query
			->where(function ($query) use ($columns, $search) {
				foreach ($columns as $column) {
					$query->orWhere($column, 'LIKE', "%$search%");
				}
			});
		return $query;
	}

	/**
	 * Permet d'ajouter un filtre de recherche sur un objet Eloquent
	 * @param 	mixed 	$query				L'objet Eloquent
	 * @param 	mixed 	$columns			Les colones où rechercher
	 * @param 	mixed 	$search				Le mot clé à rechercher
	 * @return 	mixed
	 */
	public function queryDistinct($query, $distinct)
	{
		$query->select($distinct)->distinct();
		return $query;
	}

	/**
	 * Permet d'ajouter des relation sur un model laravel via chargement load
	 * @param 	mixed 	$query				L'objet Eloquent
	 * @param 	mixed 	$requestData		Les valeurs à utiliser pour appliquer les relations
	 * @param 	mixed 	$modelName			Le nom du model
	 * @return 	mixed
	 */
	public function modelRelationLoad($model, $requestData, $modelName)
	{
		$modelPath = "App\Models\\$modelName";
		$currentObjet = new $modelPath();
		$relations = collect($requestData)->filter(function ($value, $key) {
			return strpos($key, 'with_') === 0;
		});
		$validatedRelations = [];
		foreach ($relations as $relation => $value) {
			$relation = str_replace("<", ".", substr($relation, 5));
			try {
				$currentObjet->load($relation);
				($value == "true") ? $validatedRelations[] = $relation : null;
			} catch (RelationNotFoundException $ex) {
				continue;
			}
		}
		$model->load($validatedRelations);
		return $model;
	}

	/**
	 * Vérifie si on a à faire à un base64 valide
	 * @param 	mixed	$base64				Le base64
	 * @param 	mixed	$validateType		Les types de base64 valides
	 * @return	boolean
	 */
	public function checkIsBase64Validated($base64, $validatedTypes = ["jpg", "png", "jpeg", "gif"])
	{
		$validators = [
			"jpg" => fn($base64) => strpos($base64, 'data:image/jpg;base64') === 0,
			"jpeg" => fn($base64) => strpos($base64, 'data:image/jpeg;base64') === 0,
			"png" => fn($base64) => strpos($base64, 'data:image/png;base64') === 0,
			"gif" => fn($base64) => strpos($base64, 'data:image/gif;base64') === 0,
			"py" => fn($base64) => strpos($base64, 'data:text/x-python;base64') === 0,
		];
		foreach ($validatedTypes as $validatedType) {
			if ($validators[$validatedType]($base64)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Enregistre une image
	 * @param 	string	$base64				Le base64
	 * @param 	string	$savePath			Le chemin d'enregistrement
	 * @param 	array	$validatedTypes		Les types du fichier validés
	 *
	 */
	public function saveImageFromBase64($base64, $savePath, $validatedTypes = ["jpg", "png", "jpeg", "gif"])
	{
		try {
			foreach ($validatedTypes as $extention) {
				$imageData = str_replace("data:image/$extention;base64,", '', $base64);
			}
			$imageData = str_replace(' ', '+', $imageData);
			$imageData = base64_decode($imageData);
			Storage::disk("public")->put($savePath, $imageData);
			return $savePath;
		} catch (Exception $ex) {
			return false;
		}
	}

	/**
	 * Enregistre un fichier
	 * @param 	string	$base64				Le base64
	 * @param 	string	$savePath			Le chemin d'enregistrement
	 *
	 */
	public function saveScriptFromBase64($base64, $savePath)
	{
		try {
			$imageData = str_replace("data:text/x-python;base64,", '', $base64);
			$imageData = str_replace(' ', '+', $imageData);
			$imageData = base64_decode($imageData);
			Storage::disk("public")->put($savePath, $imageData);
			return $savePath;
		} catch (Exception $ex) {
			return false;
		}
	}



	/**
	 * Permet d'ajouter des filtres avec des valeurs multiples sur un objet Eloquent
	 * @param 	mixed 	$query					L'objet Eloquent
	 * @param 	mixed 	$requestData			Les données de la requete
	 * @param 	mixed 	$correlationDataList	Les valeurs à utiliser pour appliquer les filtres
	 * @return 	mixed
	 */
	public function queryMultipeValvueFilter($query, $requestData, $correlationDataList, $prefix = "multi_")
	{
		foreach ($correlationDataList as $dbFilter => $correlationData) {
			$multiName = $prefix . $dbFilter;
			if (isset($requestData[$multiName]) && $requestData[$multiName]) {
				$query->where(function ($query) use ($dbFilter, $multiName, $requestData, $correlationData) {
					foreach (explode("-", $requestData[$multiName]) as $char) {
						if (array_key_exists($char, $correlationData)) {
							$query->orWhere($dbFilter, $correlationData[$char]);
						}
					}
				});
			}
		}
		return $query;
	}
}
