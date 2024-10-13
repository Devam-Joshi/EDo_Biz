<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\Api\Send_Money_Event;
use App\Events\Api\User_Toupop_Event;
use App\Http\Resources\Api\Transaction_Listing_Resource;
use App\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\ResponseController;
use Illuminate\Validation\Rule;
use Stripe\Stripe;


class PaymentController extends ResponseController
{
    public function add_amount(Request $request)
    {
        $user = $request->user();
        $this->directValidation([
            'amount' => ['required', 'Numeric', 'min:0.50'],
        ], ['amount.min' => __('api.err_amount_must_be_greater_then_min')]);
        $default_card = $user->defaultPaymentMethod();
        if ($default_card) {
            try {
                $amount = $request->amount;
                $admin_charge = calculate_percentage($amount, TOPUP_CHARGE);
                $chargeable_amount = ($amount + $admin_charge) * 100;
                $payment = $user->charge($chargeable_amount, $default_card);
                event(new User_Toupop_Event([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'charge' => $admin_charge,
                    'transaction_id' => $payment->id,
                ]));
                $this->sendResponse(200, __('api.succ_amount_added_in_wallet'));
            } catch (\Exception $exception) {
                $this->sendError($exception->getMessage());
            }
        } else {
            $this->sendError(__('api.err_add_card_for_topup'));
        }
    }

    public function send_money(Request $request)
    {
        $user_ids = $request->user_ids;
        $amounts = $request->amounts;
        $user_data = $request->user();
        if (count($user_ids) == count($amounts)) {
            $total_amount = collect($amounts)->sum();
            if ($user_data->wallet >= $total_amount) {
                $this->directValidation([
                    'user_ids' => ['required', 'array'],
                    'amounts' => ['required', 'array'],
                    'user_ids.*' => ['required', 'integer', Rule::exists('users', 'id')->where('type', 'user')->whereNot('id', $user_data->id)],
                    'amounts.*' => ['required', 'numeric', 'min:0.01'],
                ], [
                    'user_ids.*.exists' => __('api.err_user_not_found'),
                    'amounts.*.numeric' => __('api.err_amount_must_be_greater_then_min_for_sent')
                ]);
                $user_data->update(['wallet' => $user_data->wallet - $total_amount]);
                event(new Send_Money_Event($user_ids, $amounts, $user_data->id));
                $this->sendResponse(200, __('api.succ_amount_send_by_owner'));
            } else {
                $this->sendError(__('api.err_insufficient_wallet_amount'));
            }
        } else {
            $this->sendError('Please pass all the information properly');
        }
    }

    public function transaction_listing(Request $request)
    {
        $type = $request->type;
        $limit = $request->limit ?? 10;
        $offset = $request->offset ?? 0;
        $this->directValidation([
            'type' => ['required', 'in:1,2,3'],
        ]);
        $user_data = $request->user();
//        1==top Up &&2 ==sent && 3== recive
        $listing = Transaction::query();
        if ($type == 1) {
            $listing = $listing->where(['user_id' => $user_data->id, 'type' => 'top_up']);
        } elseif ($type == 2) {
            $listing = $listing->where(['from_user_id' => $user_data->id, 'type' => 'transfer'])->groupBy('transaction_id');
        } elseif ($type == 3) {
            $listing = $listing->where(['user_id' => $user_data->id, 'type' => 'transfer'])->groupBy('transaction_id');
        }
        $listing = $listing->limit($limit)->offset($offset)->latest('id')->get();
        $this->sendResponse(200, __('api.succ_amount_send_by_owner'), Transaction_Listing_Resource::collection($listing));
    }

    public function transaction_detail(Request $request)
    {
        $transaction_id = $request->transaction_id;
        $user_data = $request->user();
        $this->directValidation([
            'transaction_id' => ['required', Rule::exists('transactions', 'transaction_id')],
        ]);
        $transaction = Transaction::where('transaction_id', $transaction_id)->first();
        $data = [
            'id' => $transaction->id,
            'transaction_id' => $transaction->transaction_id,
            'amount' => $transaction->amount,
            'charge' => $transaction->charge,
            'total' => $transaction->total,
            'status' => $transaction->status,
            'type' => $transaction->type,
            'created_at' => $transaction->created_at,
            'updated_at' => $transaction->updated_at,
            'is_own_transaction' => ($transaction->type == 'top_up' || $transaction->from_user_id == $user_data->id) ? 1 : 0,
            'transactions' => [],
        ];
        if ($transaction->type == 'transfer') {
            $relation_ship_type = ($transaction->user_id == $user_data->id) ? "receiver" : "sender";
            $transactions = Transaction::where(['transaction_id' => $transaction_id]);
            if ($relation_ship_type == "receiver") {
                $transactions = $transactions->where('user_id', $user_data->id);
            }
            $transactions = $transactions->has($relation_ship_type)->with([$relation_ship_type => function ($user) {
                $user->SimpleDetails();
            }])->get();
            foreach ($transactions as $transaction) {
                $data['transactions'][] = [
                    'id' => $transaction->id,
                    'status' => $transaction->status,
                    'amount' => $transaction->amount,
                    'charge' => $transaction->charge,
                    'total' => $transaction->total,
                    'created_at' => $transaction->created_at,
                    'updated_at' => $transaction->updated_at,
                    'user' => $transaction[$relation_ship_type],
                ];
            }
        }
        $this->sendResponse(200, __('api.succ'), $data);
    }


    public function test(Request $request)
    {
//        $user = $request->user();
//        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
////        $account = \Stripe\Account::create([
////            'email' => $user->email,
////            'type' => 'standard',
////        ]);
//        dd(\Stripe\Account::retrieve('acct_1IIWbaLhmBtAU29d'));

        $account_links = \Stripe\AccountLink::create([
            'account' => 'acct_1IIWbaLhmBtAU29d',
            'refresh_url' => "http://127.0.0.1:8000/api/V1/payment/test",
            'return_url' => "http://127.0.0.1:8000/api/V1/payment/test",
            'type' => 'account_onboarding',
        ]);
        dd($account_links);
    }


}
