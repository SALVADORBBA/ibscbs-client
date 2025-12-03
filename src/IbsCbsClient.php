<?php

namespace DeveloperApi\IbsCbs;

class IbsCbsClient
{
    /**
     * Endpoint da API IBS/CBS
     * @var string
     */
    private string $endpoint;

    /**
     * Construtor
     *
     * @param string|null $endpoint
     */
    public function __construct(?string $endpoint = null)
    {
        $this->endpoint = $endpoint ?? 'https://modelotributacao.developerapi.com.br/ibscbs/json';
    }

    /**
     * Chamada alto nível: monta o payload padrão (nota, itens, UFs)
     *
     * @param array  $nota       ex: ['data_emissao' => '2025-07-15']
     * @param array  $items      ex: [ ['vprod'=>48.90,'ibs_classificacao'=>'000001','ibs_cst'=>'000'], ... ]
     * @param string $ufEmitente ex: 'SP'
     * @param string $ufCliente  ex: 'SP'
     *
     * @return array
     */
    public function calcular(array $nota, array $items, string $ufEmitente, string $ufCliente): array
    {
        $payload = [
            'nota'       => $nota,
            'items'      => $items,
            'ufEmitente' => $ufEmitente,
            'ufCliente'  => $ufCliente,
        ];

        return $this->post($payload);
    }

    /**
     * Chamada mais genérica: envia qualquer payload aceito pela API
     *
     * @param array $payload
     * @return array
     */
    public function post(array $payload): array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \RuntimeException('Erro ao chamar API IBS/CBS: ' . $error);
        }

        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException('API IBS/CBS retornou HTTP ' . $status . ': ' . $response);
        }

        $decoded = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Erro ao decodificar JSON: ' . json_last_error_msg());
        }

        return $decoded;
    }
}


     
    //   IBS/CBS: cálculo por item (estrutura JSON similar ao XML <IBSCBS>)
    //   Quando for ativar IBS/CBS neste script, descomente o bloco abaixo
    //   e garanta que a classe IbsCbsConfigService esteja carregada.
     
    //   Exemplo de uso real:
     
      $nota_stub = (object)[
          'data_emissao' => $getSale['date'] ?? date('Y-m-d'),
      ];
     
      $ufEmitente = $empresa['uf'] ?? 'BA';
      $ufCliente  = $getCustomer['state'] ?? $ufEmitente;
     
      $ibsResultado = IbsCbsConfigService::montarIbscbsJson(
          $nota_stub,
          (object)[
              'ibs_classificacao'    => $item['ibs_classificacao']    ?? '000001',
              'ibs_cst'              => $item['ibs_cst']              ?? '000',
              'vbc_ibs'              => $subtotal,
              'vprod'                => $subtotal,
              'ibs_indicador_doacao' => $item['ibs_indicador_doacao'] ?? null,
          ],
          $ufEmitente,
          $ufCliente,
          $index + 1,
          $ibsTotais
      );
     
     // Estrutura pronta para envio para API (quando a API suportar IBS/CBS)
      $dados['produtos'][$index]['IBSCBS']    = $ibsResultado['ibs_cbs'];
      $dados['produtos'][$index]['infAdProd'] =
          trim(($dados['produtos'][$index]['infAdProd'] ?? '') . ' ' . $ibsResultado['infAdProd']);
    