<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionRes extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $wrap = null;

    private function getReasonText($code)
    {
        $reasons = config('transactionReason.REASONS');
        return (array_key_exists($code, $reasons)) ? $reasons[$code] : $reasons[0];
    }

    public function toArray($request)
    {
        if ($this['type'] == 'transfer') {
            return [
                'id' => $this['id'],
                'type' => $this['type'],
                'amount' => $this['amount'],
                'description' => $this['descriptions']['description'],
                'reason' => $this->getReasonText($this['descriptions']['reasonCode']),
                'transferInformation' => [
                    'status' => (empty($this['failed'])),
                    'result' => (empty($this['failed'])) ? (new SuccessfulTransactionRes($this['successful'])) : (new FailedTransactionRes($this['failed'])),
                    'toUser' => new UserRes($this['toUser']),
                ]
            ];
        } else {
            return parent::toArray($request);
        }
    }
}
