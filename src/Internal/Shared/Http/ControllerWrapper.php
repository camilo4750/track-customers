<?php

namespace Internal\Shared\Http;


use Illuminate\Validation\ValidationException;
use Internal\Shared\Exceptions\ApplicationLogicException;
use Internal\Shared\Exceptions\BusinessLogicException;
use Illuminate\Support\Facades\DB;

class ControllerWrapper
{
    public static function execWithJsonSuccessResponse($callback, $fallback = null)
    {
        try {
            DB::beginTransaction();
            $response = $callback();
            $response = array_merge([
                'success' => true,
                'message' => ''
            ], $response);

            DB::commit();
        } catch (ValidationException $exception) {
            DB::rollBack();
            report($exception);
            $errors = [];
            foreach ($exception->errors() as $field => $error) {
                $errors[$field] = join(',', $error) . " ";
            }
            $response = response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Error al validar los datos',
                'errors' => $errors
            ], 422);
        } catch (ApplicationLogicException | BusinessLogicException $exception) {
            DB::rollBack();
            report($exception);
            $response = response()->json([
                'success' => false,
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'errors' => $exception->getErrors()
            ], $exception->getCode());
        } catch (\Exception $exception) {
            DB::rollBack();
            report($exception);
            $response = response()->json([
                'success' => false,
                'code' => 500,
                'message' => 'Error interno del servidor',
                'errors' => []
            ], 500);
        }
        return $response;
    }
}