<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionRes;
use App\Http\Resources\x;
use App\Models\FailedTransaction;
use App\Models\SuccessfulTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\Interfaces\FinnoTechTransferInterface;
use App\Rules\IsVerifiedUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    private FinnoTechTransferInterface $transferRepo;

    public function __construct(FinnoTechTransferInterface $repository)
    {
        $this->transferRepo = $repository;
    }

    public function createUserTransferTransaction(Request $request): TransactionRes|JsonResponse
    {
        $fromUser = Auth::user();
        $validate = validator(['from_id' => $request['id'], 'target_id' => $request['target_id'],
            'description' => $request['description'], 'amount' => $request['amount']], [
            'from_id' => ['required', Rule::exists('users', 'id'), Rule::in([$fromUser['id']]), new IsVerifiedUser],
            'target_id' => ['required', Rule::exists('users', 'id'), new IsVerifiedUser],
            'description' => ['required'],
            'amount' => ['required', 'lte:' . $fromUser['cash']]]);
        if ($validate->fails()) return Response::json(['message' => "The given data was invalid.", 'errors' => $validate->errors()], 422);
        $transaction = new Transaction();
        $transaction['type'] = 'transfer';
        $transaction['amount'] = $request['amount'];
        $transaction['descriptions'] = ['description' => $request['description'], 'reasonCode' => ($request['reason_code'] ?? 19)];
        $transaction->fromUser()->associate($fromUser);
        $targetUser = User::find($request['target_id']);
        $transaction->toUser()->associate($targetUser);
        $transaction->save();
        $result = $this->transferRepo->makeTransfer($fromUser, $targetUser, $transaction);
        if ($result['ok']) {
            $successfulTransaction = new SuccessfulTransaction();
            $successfulTransaction['inquiry_type'] = $result['inquiry_type'];
            $successfulTransaction['inquiry_ref_code'] = $result['inquiry_ref_code'];
            $successfulTransaction['inquiry_date'] = $result['inquiry_date'];
            $successfulTransaction['inquiry_time'] = $result['inquiry_time'];
            $successfulTransaction['inquiry_sequence'] = $result['inquiry_sequence'];
            $successfulTransaction->transaction()->associate($transaction);
            $successfulTransaction->save();
            $fromUser['cash'] -= $transaction['amount'];
            $fromUser->save();
            $targetUser['cash'] += $transaction['amount'];
            $targetUser->save();
        } else {
            $failedTransaction = new FailedTransaction();
            $failedTransaction['code'] = $result['code'];
            $failedTransaction['message'] = $result['message'];
            $failedTransaction->transaction()->associate($transaction);
            $failedTransaction->save();
        }
        return new TransactionRes($transaction);
    }

    public function getUserTransferTransactions()
    {
        return TransactionRes::collection(Auth::user()->transactions()->get()->load('successful', 'failed'));
    }
}
