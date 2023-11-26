<?php

namespace App\Http\Controllers;

use App\Kafka\KafkaProducer;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function responseToJSON($response): JsonResponse
    {
        // TODO
        // 'bugtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7)
        try {
            if (isset($response['error'])) {
                return response()->json(['error' => $response['error']], $response['code']);
            }

            if (isset($response['message'])) {
                return response()->json(['message' => $response['message']], $response['code']);
            }

            if ($response) {
                return response()->json($response, 200);
            }

            return response()->json(["message" => "success"], 200);
        } catch (\Exception $error) {
            return response()->json(['error' => $error->getMessage()], 500);
        }
    }

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

    public function handleKafka()
    {
        new KafkaProducer('new_users', [
            "name" => "username"
        ]);
    }
}
