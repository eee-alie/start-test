<?php

namespace App\Repositories\FinnoTech;

use App\Repositories\Interfaces\FinnoTechTransferInterface;
use Exception;
use Illuminate\Support\Facades\Http;

class FinnoTechTransferRepository implements FinnoTechTransferInterface
{
    private array $outCome = ['ok' => null, 'code' => 'LOCAL_UNEXPECTED_ACCESS', 'message' => 'wtf'];

    private function makeTransferUrl($clientId, $transactionId): string
    {
        $url = config('finnotech.TRANSFER_URL');
        $url = str_replace('{clientId}', $clientId, $url);
        return str_replace('{trackId}', "transfer$transactionId", $url);
    }

    private function getAccessToken(): string
    {
        return 'Bearer 123456789';
    }

    public function getResult(): array
    {
        return $this->outCome;
    }

    public function makeTransfer($fromUser, $toUser, $transaction)
    {
        if ($this->outCome['ok'] === null) {
            try {
                $response = Http::timeout(30)->
                withHeaders(['Authorization' => $this->getAccessToken(), 'Accept' => 'application/json', 'Content-Type' => 'application/json'])->
                post($this->makeTransferUrl($fromUser['identifier'], $transaction['id']),
                    [
                        'amount' => $transaction['amount'],
                        'description' => $transaction['descriptions']['description'],
                        'destinationFirstname' => $toUser['first_name'],
                        'destinationLastname' => $toUser['last_name'],
                        'destinationNumber' => $toUser['bank_account_number'],
                        'paymentNumber' => $transaction['id'],
                        'deposit' => $fromUser['bank_account_number'],
                        'sourceFirstName' => $fromUser['first_name'],
                        'sourceLastName' => $fromUser['last_name'],
                        'reasonDescription' => $transaction['descriptions']['reasonCode']
                    ]);
                $status = $response->status();
                if ($status == 200) {
                    $result = json_decode($response->body(), true);
                    $this->outCome = ['ok' => true,
                        'inquiry_type' => $result['result']['type'],
                        'inquiry_ref_code' => $result['result']['refCode'],
                        'inquiry_date' => $result['result']['inquiryDate'],
                        'inquiry_time' => $result['result']['inquiryTime'],
                        'inquiry_sequence' => $result['result']['inquirySequence']
                    ];
                } elseif ($status == 400) {
                    $result = json_decode($response->body(), true);
                    $this->outCome = ['ok' => false, 'code' => $result['error']['code'], 'message' => $result['error']['message']];
                } else $this->outCome = ['ok' => false, 'code' => 'STATUS_CODE_NOT_HANDLED', 'message' => $status . '|' . $response->body()];
            } catch (Exception) {
                $this->outCome = ['ok' => false, 'code' => 'LOCAL_TIMEOUT', 'message' => 'timeout(504)'];
            }
        }
        return $this->outCome;
    }
}
