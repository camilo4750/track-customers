<?php

namespace Internal\Seeders\Application\Contracts;

interface RunnableSeederInterface
{
    /**
     * Run the seeder creating the given number of records.
     *
     * @return int Number of records created
     */
    public function runWithCount(int $count): int;
}
