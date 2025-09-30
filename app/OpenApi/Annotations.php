<?php

namespace App\OpenApi;

/**
 * @OA\Info(
 *     title="Secunda API",
 *     version="1.0.0",
 *     description="REST API для справочника Организаций, Зданий и Деятельностей"
 * )
 *
 * @OA\Server(
 *     url="http://localhost",
 *     description="Local"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="api_key",
 *     type="apiKey",
 *     in="header",
 *     name="X-API-Key"
 * )
 */
class Annotations {}

/**
 * @OA\Get(
 *   path="/api/v1/activities",
 *   summary="Дерево видов деятельности",
 *   security={{"api_key":{}}},
 *
 *   @OA\Response(response=200, description="OK")
 * )
 */
class ActivitiesPath {}

/**
 * @OA\Get(
 *   path="/api/v1/buildings",
 *   summary="Список зданий",
 *   security={{"api_key":{}}},
 *
 *   @OA\Response(response=200, description="OK")
 * )
 */
class BuildingsPath {}

/**
 * @OA\Get(
 *   path="/api/v1/organizations",
 *   summary="Список организаций",
 *   security={{"api_key":{}}},
 *
 *   @OA\Parameter(name="name", in="query", description="Фильтр по названию", @OA\Schema(type="string")),
 *
 *   @OA\Response(response=200, description="OK")
 * )
 */
class OrganizationsIndexPath {}

/**
 * @OA\Get(
 *   path="/api/v1/organizations/{id}",
 *   summary="Карточка организации",
 *   security={{"api_key":{}}},
 *
 *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=404, description="Not Found")
 * )
 */
class OrganizationsShowPath {}

/**
 * @OA\Get(
 *   path="/api/v1/buildings/{building}/organizations",
 *   summary="Организации в здании",
 *   security={{"api_key":{}}},
 *
 *   @OA\Parameter(name="building", in="path", required=true, @OA\Schema(type="integer")),
 *
 *   @OA\Response(response=200, description="OK")
 * )
 */
class OrganizationsByBuildingPath {}

/**
 * @OA\Get(
 *   path="/api/v1/activities/{activity}/organizations",
 *   summary="Организации по виду деятельности (включая вложенные)",
 *   security={{"api_key":{}}},
 *
 *   @OA\Parameter(name="activity", in="path", required=true, @OA\Schema(type="integer")),
 *
 *   @OA\Response(response=200, description="OK")
 * )
 */
class OrganizationsByActivityPath {}

/**
 * @OA\Get(
 *   path="/api/v1/organizations/near",
 *   summary="Организации в радиусе от точки",
 *   security={{"api_key":{}}},
 *
 *   @OA\Parameter(name="lat", in="query", required=true, @OA\Schema(type="number", format="float")),
 *   @OA\Parameter(name="lng", in="query", required=true, @OA\Schema(type="number", format="float")),
 *   @OA\Parameter(name="radius_km", in="query", required=true, @OA\Schema(type="number", format="float")),
 *
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=422, description="Validation error")
 * )
 */
class OrganizationsNearPath {}

/**
 * @OA\Get(
 *   path="/api/v1/organizations/in-rect",
 *   summary="Организации в прямоугольной области",
 *   security={{"api_key":{}}},
 *
 *   @OA\Parameter(name="lat_min", in="query", required=true, @OA\Schema(type="number", format="float")),
 *   @OA\Parameter(name="lat_max", in="query", required=true, @OA\Schema(type="number", format="float")),
 *   @OA\Parameter(name="lng_min", in="query", required=true, @OA\Schema(type="number", format="float")),
 *   @OA\Parameter(name="lng_max", in="query", required=true, @OA\Schema(type="number", format="float")),
 *
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=422, description="Validation error")
 * )
 */
class OrganizationsInRectPath {}

/**
 * @OA\Get(
 *   path="/api/v1/organizations/search",
 *   summary="Поиск организаций",
 *   security={{"api_key":{}}},
 *
 *   @OA\Parameter(name="name", in="query", required=false, @OA\Schema(type="string")),
 *   @OA\Parameter(name="activity", in="query", required=false, @OA\Schema(type="integer")),
 *
 *   @OA\Response(response=200, description="OK"),
 *   @OA\Response(response=422, description="Validation error")
 * )
 */
class OrganizationsSearchPath {}
