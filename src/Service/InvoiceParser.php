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

    public function parse(string $fp): void
    {
        if (str_contains($fp, 'json')) {        //Pour les json
            $f = file_get_contents($fp);
            $d = preg_split("/\r\n|\n|\r/", $f);
            $c = 0;
            $m = "";
            $n = "";
            /** Tant qu'il y a une ligne */
            while(true){
                if(isset($d[$c])){
                    if(str_contains($d[$c], "montant")){
                    $m = explode(": ", $d[$c])[1];
                    $m = substr($m, 0, strlen($m) - 1);
                    }
                    if(str_contains($d[$c], "nom")){
                    $n = explode(": ", $d[$c])[1];
                    $n = substr($n, 0, strlen($n) - 1);
                    }
                    if(str_contains($d[$c], "}")){
                        $this->em->getConnection()->executeStatement(
                "UPDATE invoice SET amount = {$m} WHERE name = '{$n}'"
                    );
                    }
                    $c++;
                }else{
                break;
                }
            }
        } elseif (str_contains($fp, 'csv')) {   //Pour les json


            $d = array_map(function($r) {
                return str_getcsv($r, "\t");
            }, file($fp));
            $c = 0;
                while(true){
                if(isset($d[$c])){
                    $this->em->getConnection()->executeStatement(
                        "UPDATE invoice SET amount = {$d[$c][0]} WHERE name = '{$d[$c][2]}'"
                    );
                    $c++;
                }else{
                    break;
                }
                }
        }
    }
}
