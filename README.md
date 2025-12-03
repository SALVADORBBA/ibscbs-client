# developer-api/ibscbs-client

Cliente PHP para a API de cálculo de IBS/CBS.

- Endpoint padrão: `https://modelotributacao.developerapi.com.br/ibscbs/json`
- Classe principal: `DeveloperApi\IbsCbs\IbsCbsClient`

## Requisitos

- PHP >= 8.0 (com cURL habilitado)
- Composer
- Acesso HTTP ao endpoint da API

## Instalação

Se o pacote ainda não estiver no Packagist, adicione o repositório Git no `composer.json` do seu projeto:

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/salvadorbba/ibscbs-client.git"
    }
  ],
  "require": {
    "developer-api/ibscbs-client": "dev-main"
  }
}
```

Em seguida, instale:

```bash
composer require developer-api/ibscbs-client
```

## Uso

### 1) Endpoint padrão

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use DeveloperApi\IbsCbs\IbsCbsClient;

$client = new IbsCbsClient(); // usa o endpoint padrão

$nota = [
    'data_emissao' => '2025-07-15',
];

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
    echo '<pre>';
    print_r($resultado);
    echo '</pre>';
} catch (\Throwable $e) {
    echo 'Erro na API IBS/CBS: ' . $e->getMessage();
}
```

### 2) Endpoint customizado

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use DeveloperApi\IbsCbs\IbsCbsClient;

$client = new IbsCbsClient('https://modelotributacao.developerapi.com.br/public/api/ibscbs/json');

$nota = [
    'data_emissao' => '2025-07-15',
];
 
$items = [
    [
        'vprod'             => 48.90,
        'ibs_classificacao' => '000001',
        'ibs_cst'           => '000',
    ],
];

try {
    $resultado = $client->calcular($nota, $items, 'MT', 'MT');
    echo '<pre>';
    print_r($resultado);
    echo '</pre>';
} catch (\Throwable $e) {
    echo 'Erro na API IBS/CBS (endpoint customizado): ' . $e->getMessage();
}
```

## Payload

Formato enviado pelo método `calcular()`:

```json
{
  "nota": {
    "data_emissao": "2025-07-15"
  },
  "items": [
    {
      "vprod": 48.9,
      "ibs_classificacao": "000001",
      "ibs_cst": "000"
    },
    {
      "vprod": 52.1,
      "ibs_classificacao": "000001",
      "ibs_cst": "000"
    }
  ],
  "ufEmitente": "SP",
  "ufCliente": "SP"
}
```

Campos:

- `nota.data_emissao`: data de emissão (YYYY-MM-DD)
- `items[].vprod`: valor do produto
- `items[].ibs_classificacao`: classificação IBS/CBS (cClassTrib)
- `items[].ibs_cst`: CST IBS/CBS
- `ufEmitente`: UF do emitente (ex.: SP, MT)
- `ufCliente`: UF do destinatário/cliente

## Tratamento de erros

O client pode lançar exceções quando:

- Falha na conexão cURL (rede, timeout, DNS, SSL)
- Resposta HTTP não bem-sucedida (status >= 300)
- Erro ao decodificar JSON

Use `try/catch` ao redor das chamadas:

```php
try {
    $resultado = $client->calcular($nota, $items, 'SP', 'SP');
} catch (\RuntimeException $e) {
    // trate erro de chamada/HTTP/JSON
    echo $e->getMessage();
}
```

## cURL (alternativo)

Chamada direta sem usar o client:

```php
<?php
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL            => 'https://modelotributacao.developerapi.com.br/public/api/ibscbs/json',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING       => '',
    CURLOPT_MAXREDIRS      => 10,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => 'POST',
    CURLOPT_POSTFIELDS     => '{
      "nota": { "data_emissao": "2025-07-15" },
      "items": [ { "vprod": 48.90, "ibs_classificacao": "000001", "ibs_cst": "000" } ],
      "ufEmitente": "MT",
      "ufCliente": "MT"
    }',
    CURLOPT_HTTPHEADER     => [ 'Content-Type: application/json' ],
]);

$response = curl_exec($curl);

if ($response === false) {
    echo 'Erro cURL: ' . curl_error($curl);
} else {
    echo $response;
}

curl_close($curl);
```

## Autor

- Nome: Rubens dos Santos
- E-mail: salvadorbba@gmail.com  

## Licença

Este pacote é distribuído sob licença MIT (veja `composer.json`).
