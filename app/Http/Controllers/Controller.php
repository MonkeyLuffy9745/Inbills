<?php

namespace App\Http\Controllers;

use App\Http\Traits\ControllerHelperTrait;
use App\Http\Traits\CustomResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
	use AuthorizesRequests, ValidatesRequests, CustomResponseTrait, ControllerHelperTrait;

	/**
	 * Enregistrer un model
	 * @param 	mixed 		$modelClass			La classe du model
	 * @param 	array 		$requestData		Les données de la requête
	 * @param 	array 		$validations		Les données des validations à effectuer
	 * @param 	array 		$validationsText	Les textes des validations à effectuer
	 * @param 	array 		$manualValidations	Une fonction de validations manuelles
	 * @param 	callable 	$beforeCreate		Une fonction à appeler avant l'insertion, params : requestData: Les données de la requête, manualValidationsReturnData: les données retournées par la validation manuelle
	 * @param 	callable 	$afterCreate		Une fonction à appeler après l'insertion, params : model: le model fraichement crée, requestData: Les données de la requête, manualValidationsReturnData: les données retournées par la validation manuelle
	 * @param 	callable 	$beforeCommit		Une fonction à appeler avant le commit, params : model: le model fraichement crée, requestData: Les données de la requête, manualValidationsReturnData: les données retournées par la validation manuelle
	 * @param 	callable 	$afterCommit		Une fonction à appeler après le commit, params : model: le model fraichement crée, requestData: Les données de la requête, manualValidationsReturnData: les données retournées par la validation manuelle
	 * @param 	string	    $authName   		Le nom de la fonction de police à utiliser
	 * @param 	array	    $relations   		Les relations à afficher lors de retour
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function modelStore($modelClass, array $requestData, array $validations = [], array $validationsText = [], callable $manualValidations = null, callable $beforeCreate = null, callable $afterCreate = null, callable $beforeCommit = null, callable $afterCommit = null, string $authName = "create", $relations = [])
	{
		if ($authName && !($authorisation = Gate::inspect($authName, $modelClass))->allowed()) {
			return $this->responseError(["auth" => [$authorisation->message()]], 403);
		}
		$validator = Validator::make($requestData, $validations, $validationsText);
		if ($validator->fails()) {
			return $this->responseError($validator->errors(), 400);
		}
		DB::beginTransaction();
		$manualValidationsReturn = ($manualValidations) ? $manualValidations($requestData) : null;
		if (isset($manualValidationsReturn["errors"])) {
			if ($manualValidationsReturn["errors"]) {
				return $this->responseError($manualValidationsReturn["errors"], $manualValidationsReturn["status"] ?? 400);
			}
		}
		$manualValidationsReturn["data"] = isset($manualValidationsReturn["data"]) ? $manualValidationsReturn["data"] : [];
		$requestData = ($beforeCreate) ? $beforeCreate($requestData, $manualValidationsReturn["data"]) : $requestData;
		$model = call_user_func_array([$modelClass, 'create'], [$requestData]);
		$model = ($afterCreate) ? $afterCreate($model, $requestData, $manualValidationsReturn["data"]) : $model;
		$modelClassExployed = explode("\\", $modelClass);
		$model = ($beforeCommit) ? $beforeCommit($model, $requestData, $manualValidationsReturn["data"]) : $model;
		DB::commit();
		$model = ($afterCommit) ? $afterCommit($model, $requestData, $manualValidationsReturn["data"]) : $model;
		$model = $relations ? $this->modelRelationLoad($model, $relations, end($modelClassExployed)) : $model;
		return $this->responseOk([
			lcfirst(end($modelClassExployed)) => $model
		], status: 201);
	}

	/**
	 * Mettre à jour un model
	 * @param 	mixed 		$modelId			L'ID du model
	 * @param 	mixed 		$modelClass			La classe du model
	 * @param 	array 		$requestData		Les données de la requête
	 * @param 	array 		$validations		Les données des validations à effectuer
	 * @param 	array 		$validationsText	Les textes des validations à effectuer
	 * @param 	array 		$manualValidations	Une fonction de validations manuelles
	 * @param 	callable 	$beforeUpdate		Une fonction à appeler avant la mise à jour, params : model: le model actuel, requestData: Les données de la requête, manualValidationsReturnData: les données retournées par la validation manuelle
	 * @param 	callable 	$afterUpdate		Une fonction à appeler après la mise à jour, params : model: le model fraichement mis à jour, manualValidationsReturnData: les données retournées par la validation manuelle, requestData: Les données de la requête
	 * @param 	callable 	$beforeCommit		Une fonction à appeler avant le commit, params : model: le model fraichement mis à jour, manualValidationsReturnData: les données retournées par la validation manuelle, requestData: Les données de la requête
	 * @param 	callable 	$afterCommit		Une fonction à appeler après le commit, params : model: le model fraichement mis à jour, manualValidationsReturnData: les données retournées par la validation manuelle, requestData: Les données de la requête
	 * @param 	string	    $authName   		Le nom de la fonction de police à utiliser
	 * @param 	array	    $relations   		Les relations à afficher lors de retour
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function modelUpdate(mixed $modelId, $modelClass, array $requestData, array $validations = [], array $validationsText = [], callable $manualValidations = null, callable $beforeUpdate = null, callable $afterUpdate = null, callable $beforeCommit = null, callable $afterCommit = null, $authName = "update", $elementName = "L'élément", $relations = [])
	{
		if ($modelId) {
			$validator = Validator::make($requestData, $validations, $validationsText);
			if ($validator->fails()) {
				return $this->responseError($validator->errors(), 400);
			}
			$modelClassExployed = explode("\\", $modelClass);
			$model = call_user_func_array([$modelClass, 'find'], [$modelId]);
			$modelClassName = lcfirst(end($modelClassExployed));
			if ($model) {
				if ($authName && !($authorisation = Gate::inspect($authName, $model))->allowed()) {
					return $this->responseError(["auth" => [$authorisation->message()]], 403);
				}
				DB::beginTransaction();
				$manualValidationsReturn = ($manualValidations) ? $manualValidations($requestData, $model) : null;
				if (isset($manualValidationsReturn["errors"])) {
					if ($manualValidationsReturn["errors"]) {
						return $this->responseError($manualValidationsReturn["errors"], $manualValidationsReturn["status"] ?? 400);
					}
				}
				$manualValidationsReturn["data"] = isset($manualValidationsReturn["data"]) ? $manualValidationsReturn["data"] : [];
				$requestData = ($beforeUpdate) ? $beforeUpdate($model, $requestData, $manualValidationsReturn["data"]) : $requestData;
				$model->update($requestData);
				$model = (($afterUpdate) ? $afterUpdate($model, $requestData, $manualValidationsReturn["data"]) : $model) ?? $model;
				$model = ($beforeCommit) ? $beforeCommit($model, $requestData, $manualValidationsReturn["data"]) : $model;
				$model = $relations ? $this->modelRelationLoad($model, $relations, end($modelClassExployed)) : $model;
				DB::commit();
				$model = ($afterCommit) ? $afterCommit($model, $requestData, $manualValidationsReturn["data"]) : $model;
				return $this->responseOk([
					$modelClassName => $model
				]);
			} else {
				return $this->responseError(["id" => "$elementName n'existe pas"], 404);
			}
		} else {
			return $this->responseError(["id" => "$elementName est manquant"], 401);
		}
	}

	/**
	 * Supprimer un model
	 * @param 	mixed 		$modelId			L'ID du model
	 * @param 	mixed 		$modelClass			La classe du model
	 * @param 	array 		$manualValidations	Une fonction de validations manuelles
	 * @param 	callable 	$beforeDelete		Une fonction à appeler avant la suppression
	 * @param 	callable 	$afterDelete		Une fonction à appeler après la suppression
	 * @param 	string	    $authName   		Le nom de la fonction de police à utiliser
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function modelDelete(int $modelId, $modelClass, callable $manualValidations = null, callable $beforeDelete = null, callable $afterDelete = null, $authName = "delete", $elementName = "L'élément")
	{
		$modelClassExployed = explode("\\", $modelClass);
		$model = call_user_func_array([$modelClass, 'find'], [$modelId]);
		$modelClassName = lcfirst(end($modelClassExployed));
		if ($model) {
			if ($authName && !($authorisation = Gate::inspect($authName, $model))->allowed()) {
				return $this->responseError(["auth" => [$authorisation->message()]], 403);
			}
			$manualValidationsErrors = ($manualValidations) ? $manualValidations() : null;
			if ($manualValidationsErrors) {
				return $manualValidationsErrors;
			}
			($beforeDelete) ? $beforeDelete() : null;
			if ($model->delete()) {
				$model = ($afterDelete) ? $afterDelete($model) : $model;
				return $this->responseOk(messages: [$modelClassName => "$elementName a été supprimé"]);
			} else {
				return $this->responseError(["server" => "Erreur du serveur"], 500);
			}
		} else {
			return $this->responseError(["id" => "$elementName n'existe pas"], 404);
		}
	}
}
