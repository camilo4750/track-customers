<?php

namespace Internal\Seeders\Application\Run;

use Internal\Seeders\Application\Contracts\RunnableSeederInterface;
use Internal\Shared\Exceptions\ApplicationLogicException;

class RunSeederHandler
{
    public function handle(string $key, int $count): array
    {
        if (app()->environment('production')) {
            throw new ApplicationLogicException(
                [],
                'Los seeders solo pueden ejecutarse en entorno de desarrollo.',
                403
            );
        }

        $seeders = config('seeders.seeders', []);
        if (! isset($seeders[$key])) {
            throw new ApplicationLogicException(
                [],
                "Seeder desconocido: {$key}.",
                404
            );
        }

        $seederClass = $seeders[$key]['seederClass'] ?? null;
        if (! $seederClass || ! is_subclass_of($seederClass, RunnableSeederInterface::class)) {
            throw new ApplicationLogicException(
                [],
                "Clase de seeder no válida para: {$key}.",
                500
            );
        }

        /** @var RunnableSeederInterface $seeder */
        $seeder = app($seederClass);
        $created = $seeder->runWithCount($count);

        return [
            'created' => $created,
            'message' => "Se crearon {$created} registros correctamente.",
        ];
    }
}
