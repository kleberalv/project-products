<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

        // Tratamento de exceções para API (retorna JSON padronizado)
        $this->renderable(function (Throwable $e, $request) {
            if ($request->is('api/*')) {
                return $this->handleApiException($request, $e);
            }
        });
    }

    /**
     * Tratamento customizado de exceções para API
     */
    private function handleApiException($request, Throwable $exception)
    {
        // Model não encontrado
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Recurso não encontrado',
            ], 404);
        }

        // Rota não encontrada
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'message' => 'Endpoint não encontrado',
            ], 404);
        }

        // Erro de autenticação
        if ($exception instanceof AuthenticationException) {
            return response()->json([
                'message' => 'Não autenticado',
            ], 401);
        }

        // Validação (já tratada nos FormRequests, mas por segurança)
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $exception->errors(),
            ], 422);
        }

        // HttpException genérica
        if ($exception instanceof HttpException) {
            return response()->json([
                'message' => $exception->getMessage() ?: 'Erro no servidor',
            ], $exception->getStatusCode());
        }

        // Erro genérico (500)
        $statusCode = method_exists($exception, 'getStatusCode') 
            ? $exception->getStatusCode() 
            : 500;

        $message = config('app.debug') 
            ? $exception->getMessage() 
            : 'Erro interno do servidor';

        return response()->json([
            'message' => $message,
            'trace' => config('app.debug') ? $exception->getTrace() : null,
        ], $statusCode);
    }
}
