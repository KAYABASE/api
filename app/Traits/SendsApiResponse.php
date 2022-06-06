<?php

namespace App\Traits;

use App\Services\ApiResponse;

trait SendsApiResponse
{
    /**
     * @param array $data
     * @param null $view
     * @param string $message
     * @return ApiResponse|string|null
     */
    public function success($data = [], $view = null, string $message = "Process successfully completed!"): ApiResponse|string|null
    {
        $response = ApiResponse::create()->message($message);

        if ($view) {
            $response->view($view);
        }

        if ($data) {
            $response->data($data);
        }

        return $response;
    }

    /**
     * @param string $message
     * @param int $status
     * @param array $data
     * @param null $route
     * @return ApiResponse|int|string|null
     */
    public function error(string $message = "Error occured when process!", $status = 400, $data = [], $route = null): ApiResponse|int|string|null
    {
        $response = ApiResponse::create()->message($message)->status($status);

        if ($route) {
            $response->route($route);
        }

        if ($data) {
            $response->data($data);
        }

        return $response;
    }

    /**
     * @param $view
     * @param null $message
     * @param array $data
     * @return ApiResponse|string|null
     */
    public function view($view, $message = null, $data = []): ApiResponse|string|null
    {
        $response = ApiResponse::create()->view($view);

        if ($message) {
            $response->message($message);
        }

        if ($data) {
            $response->data($data);
        }

        return $response;
    }

    /**
     * @param null $route
     * @param null $message
     * @param null $data
     * @return ApiResponse
     */
    public function redirect($route = null, $message = null, $data = null): ApiResponse
    {
        $response = ApiResponse::create();

        if ($route) {
            $response->route($route);
        }

        if ($message) {
            $response->message($message);
        }

        if ($data) {
            $response->data($data);
        }

        return $response;
    }
}
