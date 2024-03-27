<?php

namespace App\Exceptions;

use App\Http\Responses\JsonApiValidationErrorResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /* Esta es la solución final para el método invalidJson() una vez creada una clase específica para
    los errores de validación en formato json:api, según se sugiere en
                14.- Cómo modificar los errores de validación en Laravel
    Más abajo vemos comentadas las otras dos soluciones:
        - con foreach y,
        - con colecciones

    Las tres funcionan correctamente
    */
    public function invalidJson($request, ValidationException $exception): JsonResponse
    {
        if(! $request->routeIs('api.v1.login')){
            return new JsonApiValidationErrorResponse($exception);
        }
        return parent::invalidJson($request, $exception);
    }

    /* ---------------------------   Solución con foreach --------------------------------------- */
//    public function invalidJson($request, ValidationException $exception)
//    {
//        $errors = [];
//        $title = $exception->getMessage();
//
//        foreach ($exception->errors() as $field => $message) {
//            $pointer = '/' . str_replace('.', '/', $field);
//            $errors[] = [
//                'title' => $title,
//                'detail' => $message[0],
//                'source' => [
//                    'pointer' => $pointer
//                ]
//            ];
//        }
//
//        return response()->json([
//            'errors' => $errors,
//        ], 422);
//    }

    /* --------------------  Solución con colecciones  ----------------------------------- */
//    public function invalidJson($request, ValidationException $exception)
//    {
//        $errors = [];
//        $title = $exception->getMessage();
//
//        $errors = collect($exception->errors())->
//        map(function ($message, $field) use ($title) {
//            return [
//                'title' => $title,
//                'detail' => $message[0],
//                'source' => [
//                    'pointer' => '/' . str_replace('.', '/', $field)
//                ]];
//        })->values();
//
//        return response()->json([
//            'errors' => $errors
//        ], 422);
//    }
}
