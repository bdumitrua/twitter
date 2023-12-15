<?php

namespace App\Http\Controllers;

use App\Kafka\KafkaProducer;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * @param mixed $response
     * 
     * @return JsonResponse
     */
    protected function responseToJSON($response): JsonResponse
    {
        try {
            if (empty($response)) {
                return response(null, 200);
            }

            if ($response instanceof JsonResource) {
                return response()->json($response);
            }

            if ($response instanceof Response) {
                $code = $response->getStatusCode();
                $content = empty($response->getContent()) ? null : $response->getContent();

                return response()->json($content, $code);
            }

            return response()->json(null, 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

    /**
     * @param callable $serviceFunction
     * 
     * @return JsonResponse
     */
    protected function handleServiceCall(callable $serviceFunction): JsonResponse
    {
        try {
            $response = $serviceFunction();
            return $this->responseToJSON($response);
        } catch (HttpException $exception) {
            return $this->responseToJSON([
                'error' => $exception->getMessage(),
                'code' => $exception->getStatusCode()
            ]);
        } catch (\Throwable $exception) {
            return $this->responseToJSON([
                'error' => $exception->getMessage(),
                'code' => Response::HTTP_BAD_GATEWAY
            ]);
        }
    }
}
