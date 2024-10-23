<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Exceptions\JsonReadException;
use App\Exceptions\JsonWriteException;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\InvalidRequestException;
use Illuminate\Validation\UnauthorizedException;

abstract class Controller
{
    // Manejo de excepciones
    protected function handleRequest(callable $callback)
    {
        try {
            return $callback();
        } catch (ProductNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (JsonReadException | JsonWriteException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } catch (InvalidRequestException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (UnauthorizedException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        } catch (Exception $e) {
            Log::error('Ocurrió un error al procesar una solicitud', [
                'message' => $e->getMessage() ?? null,
                'code' => $e->getCode() ?? null,
                'fecha' => date_create('now', timezone_open('America/Argentina/Buenos_Aires'))->format('Y-m-d H:i:s')
            ]);
            return response()->json([
                'error' => 'Ocurrió un error al procesar la solicitud',
                'details' => $e->getMessage()
            ], $e->getCode() >= 200 ? $e->getCode() : 500);
        }
    }    
}
