<?php

namespace App\Services;

use Throwable;
use App\Models\BaseModel as Model;
use App\Http\Resources\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * Class ApiResponse
 *
 * @package App\Services
 */
class ApiResponse implements Responsable
{
    /**
     * @var ?string
     */
    private ?string $_view;

    /**
     * @var ?array
     */
    private ?array $_route;

    /**
     * @var array|Collection
     */
    private $_data;

    /**
     * @var int
     */
    private int $_status;

    /**
     * @var ?string
     */
    private ?string $_message;

    /**
     * @var ?bool
     */
    private ?bool $_render_view;

    /**
     * ApiResponse constructor.
     */
    public function __construct()
    {
        $this->_view = null;
        $this->_route = null;
        $this->_data = [];
        $this->_status = 200;
        $this->_message = null;
        $this->_render_view = false;
    }

    /**
     * @return \App\Services\ApiResponse
     */
    public static function create(): ApiResponse
    {
        return new self();
    }

    /**
     * @param ?string $view
     *
     * @return $this|string|null
     */
    public function view($view = null)
    {
        if (!$view) {
            return $this->_view;
        }

        $this->_view = $view;

        return $this;
    }

    /**
     * @param mixed ...$route
     *
     * @return $this|array|null
     */
    public function route(...$route)
    {
        if (!$route) {
            return $this->_route;
        }

        $this->_route = $route;

        return $this;
    }

    /**
     * @param array|Collection $data
     *
     * @return $this|array|Collection
     */
    public function data($data = null)
    {
        if (is_null($data)) {
            return $this->_data;
        }

        $this->_data = $data;

        return $this;
    }

    /**
     * @param ?int $status
     *
     * @return $this|int
     */
    public function status($status = null)
    {
        if (!$status) {
            return $this->_status;
        }

        $this->_status = $status;

        return $this;
    }

    /**
     * @param ?string $message
     *
     * @return $this|string|null
     */
    public function message($message = null)
    {
        if (!$message) {
            return $this->_message;
        }

        $this->_message = $message;

        return $this;
    }

    /**
     * @param ?bool $render_view
     *
     * @return $this|bool|null
     */
    public function renderView($render_view = null)
    {
        if (is_null($render_view)) {
            $render_view = request()->input('render_view', $this->_render_view);

            if (!is_bool($render_view)) {
                $this->_render_view = json_decode($render_view);
            }

            return $this->_render_view;
        }

        $this->_render_view = $render_view;

        return $this;
    }

    /**
     * @return Renderable|JsonResponse|RedirectResponse
     */
    public function respond()
    {
        $view = $this->view();
        $route = $this->route();
        $is_ajax = $this->isAjax();
        $status = $this->status();
        $message = $this->message();
        $data = $this->data() ?? [];
        $render_view = $this->renderView();

        $prepared_data['data'] = $data;

        if ($is_ajax && !($render_view && view()->exists($view))) {
            if ($message) {
                $prepared_data = array_merge([
                    'message' => $message,
                ], $prepared_data);
            }

            return response()->json($prepared_data, $status);
        }

        if ($view) {
            try {
                $view = view()->make($view, $data);

                if ($message) {
                    $view->with(['message' => $message]);
                }

                if ($is_ajax && $render_view) {
                    return response()->json(array_merge($prepared_data, ['view' => $view->renderSections()]));
                }
            } catch (BindingResolutionException | Throwable $e) {
                // return response()->json(['error_message' => 'View template rendering error!', 'error_detail' => $e->getMessage()], 400);
            }

            return $view;
        }

        $response = $route ? redirect()->route(...$route) : redirect()->back();

        return $response->withInput()->with(['message' => $message]);
    }

    /**
     * @return bool
     */
    private function isAjax(): bool
    {
        return request()->wantsJson() || request()->ajax() || request()->isJson();
    }

    /**
     * @return bool
     */
    private function isError(): bool
    {
        return $this->status() >= 400;
    }

    /**
     * @param $value
     *
     * @return Model
     * @noinspection PhpMissingReturnTypeInspection
     */
    private function getModelFromValue($value)
    {
        /** @var Model $model */
        $model = $value->resource;

        if (is_a($model, LengthAwarePaginator::class) || is_a($model, Collection::class)) {
            $model = $model->first();

            if (is_a($model, Resource::class)) {
                $model = $model->resource;
            }
        }

        if (!is_a($model, Model::class)) {
            return null;
        }

        return $model;
    }

    public function toResponse($request)
    {
        return $this->respond();
    }
}
