<?php

declare(strict_types=1);


namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;


class InvoiceParser
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function parse(string $filePath): void
    {
        if (str_contains($filePath, 'json')) {
            $data = json_decode(file_get_contents($filePath), true);
            foreach ($data as $row) {
                $this->em->getConnection()->executeStatement(
                    "UPDATE invoice SET amount = {$row['montant']} WHERE name = '{$row['nom']}'"
                );
            }
        } elseif (str_contains($filePath, 'csv')) {
            $data = array_map(function($row) {
                return str_getcsv($row, "\t");
            }, file($filePath));
            foreach ($data as $row) {
                $this->em->getConnection()->executeStatement(
                    "UPDATE invoice SET amount = {$row[0]} WHERE name = '{$row[2]}'"
                );
            }
        }
    }
}
