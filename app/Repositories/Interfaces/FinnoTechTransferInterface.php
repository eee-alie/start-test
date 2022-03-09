<?php

namespace App\Repositories\Interfaces;

interface FinnoTechTransferInterface
{

    public function getResult(): array;

    public function makeTransfer($fromUser, $toUser, $transaction);
}
