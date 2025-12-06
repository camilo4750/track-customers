<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    /**
     * Verificación de seguridad: asegura que los tests usen BD de pruebas.
     * Solo se ejecuta si el test está usando la base de datos.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->ensureTestingDatabase();
    }

    /**
     * Verifica que los tests estén usando la base de datos de pruebas correcta.
     * Esto previene accidentalmente usar la base de datos de desarrollo/producción.
     */
    protected function ensureTestingDatabase(): void
    {
        // Solo verificar si hay una conexión de BD configurada
        // Esto permite que tests unitarios sin BD funcionen normalmente
        try {
            $connection = DB::connection()->getName();
            $database = DB::connection()->getDatabaseName();
            
            // Verificar que estamos usando SQLite en memoria para tests
            if ($connection !== 'sqlite' || $database !== ':memory:') {
                throw new \RuntimeException(
                    "⚠️ SEGURIDAD: Los tests deben usar SQLite en memoria. " .
                    "Conexión actual: {$connection}, Base de datos: {$database}. " .
                    "Verifica tu configuración en phpunit.xml"
                );
            }
        } catch (\Exception $e) {
            // Si no hay conexión de BD configurada, está bien (tests unitarios sin BD)
            // Solo lanzar la excepción si es nuestro error de seguridad
            if (str_contains($e->getMessage(), 'SEGURIDAD')) {
                throw $e;
            }
        }
    }
}
