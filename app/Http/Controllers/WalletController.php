<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class WalletController extends Controller
{
    public function __construct(private WalletService $wallet) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $wallet = $this->wallet->getOrCreate($user);
        $transactions = $this->wallet->history($user);

        return view('wallet.index', compact('wallet', 'transactions'));
    }

    public function showTopup(Request $request): View
    {
        $user = $request->user();
        $wallet = $this->wallet->getOrCreate($user);
        $returnTo = $request->query('return_to');

        return view('wallet.topup', compact('wallet', 'returnTo'));
    }

    public function submitTopup(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:10000', 'max:10000000'],
            'method' => ['required', 'in:va_bca,va_bni,va_bri,va_mandiri,va_bsi'],
            'outcome' => ['required', 'in:success,fail'],
            'return_to' => ['nullable', 'string', 'max:50'],
        ]);

        if ($data['outcome'] === 'fail') {
            return redirect()->route('wallet.topup', ['return_to' => $data['return_to'] ?? null])
                ->with('error', 'Top up gagal (simulasi). Saldo tidak berubah.');
        }

        try {
            $this->wallet->credit(
                $request->user(),
                (float) $data['amount'],
                'topup',
                'Top up via ' . strtoupper(str_replace('va_', '', $data['method']))
            );
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()])->withInput();
        }

        // Kalau dari checkout, lanjutkan ke halaman bayar (auto-settle SeumpamaPay)
        if (! empty($data['return_to'])) {
            $checkout = \App\Models\Checkout::where('reference', $data['return_to'])
                ->where('user_id', $request->user()->id)
                ->first();

            if ($checkout && $checkout->status === \App\Enums\CheckoutStatus::PendingPayment) {
                return redirect()->route('checkout.pay', $checkout->reference)
                    ->with('status', 'topup-success');
            }
        }

        return redirect()->route('wallet.index')
            ->with('status', 'topup-success')
            ->with('topup_amount', $data['amount']);
    }
}