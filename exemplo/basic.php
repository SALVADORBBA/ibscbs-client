<?php

require __DIR__ . '/vendor/autoload.php';

use DeveloperApi\IbsCbs\IbsCbsClient;

// Se você quiser forçar o endpoint do seu servidor Laravel:
$client = new IbsCbsClient();

// Dados da nota
$nota = [
    'data_emissao' => '2025-07-15',
];

// Itens da nota
$items = [
    [
        'vprod'             => 48.90,
        'ibs_classificacao' => '000001',
        'ibs_cst'           => '000',
    ],
    [
        'vprod'             => 52.10,
        'ibs_classificacao' => '000001',
        'ibs_cst'           => '000',
    ],
];

try {
    $resultado = $client->calcular($nota, $items, 'SP', 'SP');

    // Aqui você trata o retorno (salva em banco, integra com NF-e, etc.)

    echo '<pre>';
    print_r($resultado);
} catch (\Throwable $e) {
    echo 'Erro na API IBS/CBS: ' . $e->getMessage();
}
