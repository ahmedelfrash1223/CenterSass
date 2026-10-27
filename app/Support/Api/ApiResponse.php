<?php

namespace App\Support\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    // =============================================
    // Success
    // =============================================

    public static function success(
        mixed $data = null,
        string $message = 'تمت العملية بنجاح',
        int $status = 200,
        array $extra = [],
    ): JsonResponse {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $extra), $status);
    }

    /**
     * Success with a JsonResource.
     */
    public static function resource(
        mixed $resource,
        string $message = 'تمت العملية بنجاح',
        int $status = 200,
        array $extra = [],
    ): JsonResponse {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'data' => $resource->resolve(),
        ], $extra), $status);
    }

    /**
     * Success with ResourceCollection.
     */
    public static function collection(
        ResourceCollection $resource,
        string $message = 'تمت العملية بنجاح',
        int $status = 200,
        array $extra = [],
    ): JsonResponse {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'data' => $resource->resolve(),
        ], $extra), $status);
    }

    /**
     * Paginated response.
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        string $resourceClass,
        string $message = 'تمت العملية بنجاح',
    ): JsonResponse {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resourceClass::collection($paginator->items())->resolve(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ]);
    }

    // =============================================
    // Errors
    // =============================================

    public static function error(
        string $message,
        array $errors = [],
        int $status = 400,
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    public static function validationError(
        array $errors,
        string $message = 'بيانات غير صحيحة',
    ): JsonResponse {
        return self::error($message, $errors, 422);
    }

    public static function notFound(string $message = 'المورد غير موجود'): JsonResponse
    {
        return self::error($message, [], 404);
    }

    public static function unauthorized(string $message = 'غير مصرح'): JsonResponse
    {
        return self::error($message, [], 401);
    }

    public static function forbidden(string $message = 'غير مسموح'): JsonResponse
    {
        return self::error($message, [], 403);
    }

    public static function tooManyRequests(string $message = 'محاولات كثيرة جدًا. حاول لاحقًا.'): JsonResponse
    {
        return self::error($message, [], 429);
    }

    // =============================================
    // Auth specific
    // =============================================

    public static function auth(
        string $token,
        mixed $accountResource,
        string $message = 'تمت العملية بنجاح',
        int $status = 200,
        array $extra = [],
    ): JsonResponse {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'account' => $accountResource->resolve(),
            ],
        ], $extra), $status);
    }
}
