<?php

namespace App\Exceptions;

use Flugg\Responder\Exceptions\Handler as ExceptionHandler;
use Flugg\Responder\Exceptions\Http\HttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException as BaseModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (UnauthorizedException $exception) {
            return responder()->error('forbidden', __('errors.unauthorized'))->respond(Response::HTTP_FORBIDDEN);
        });
        $this->renderable(function (\Exception $exception) {
            return responder()->error(
                'server_error',
                config('app.env') === 'local'
                    ? $exception->getMessage()
                    : __('errors.server_error')
            )->respond(Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception|\Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, $exception)
    {
        $this->convert($exception, [
            BaseModelNotFoundException::class => ModelNotFoundException::class,
        ]);

        $this->convertDefaultException($exception);

        if ($exception instanceof HttpException) {
            return $this->renderResponse($exception);
        }

        if ($exception instanceof QueryException) {
            return responder()->error(
                'db_error',
                config('app.env') === 'local'
                    ? $exception->getMessage()
                    : __('errors.server_error')
            )->respond(Response::HTTP_FAILED_DEPENDENCY);
        }

        return parent::render($request, $exception);
    }
}
