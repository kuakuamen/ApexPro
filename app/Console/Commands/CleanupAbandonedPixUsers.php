<?php

namespace App\Console\Commands;

use App\Models\ProfessionalSubscription;
use App\Models\SubscriptionTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanupAbandonedPixUsers extends Command
{
    protected $signature   = 'subscriptions:cleanup-abandoned-pix';
    protected $description = 'Remove usuários criados via PIX que não pagaram e o QR Code expirou';

    public function handle(): int
    {
        // Buscar transações PIX expiradas e ainda pendentes
        $expiredTransactions = SubscriptionTransaction::query()
            ->where('payment_method', 'pix')
            ->where('status', 'pending')
            ->whereNotNull('pix_expires_at')
            ->where('pix_expires_at', '<', Carbon::now())
            ->get();

        $deleted = 0;

        foreach ($expiredTransactions as $txn) {
            $user = User::find($txn->user_id);

            if (!$user) {
                // Transação órfã — limpa só a transação
                $txn->delete();
                continue;
            }

            // Segurança 1: não apagar usuário ativo
            if ($user->is_active) {
                continue;
            }

            // Segurança 2: não apagar se já teve algum pagamento aprovado
            $hasApprovedPayment = SubscriptionTransaction::where('user_id', $user->id)
                ->where('status', 'approved')
                ->exists();

            if ($hasApprovedPayment) {
                continue;
            }

            // Segurança 3: não apagar se a assinatura já foi active em algum momento
            $subscription = ProfessionalSubscription::where('user_id', $user->id)->first();
            if ($subscription && $subscription->starts_at !== null) {
                // starts_at só é preenchido quando a assinatura é ativada
                continue;
            }

            // Tudo ok: é um novo usuário que abandonou o PIX — remover
            DB::transaction(function () use ($user, $txn, $subscription) {
                $txn->delete();
                if ($subscription) {
                    $subscription->delete();
                }
                $user->delete();
            });

            Log::info('CleanupAbandonedPix: usuário removido', [
                'user_id'    => $user->id,
                'email'      => $user->email,
                'created_at' => $user->created_at,
                'pix_expired_at' => $txn->pix_expires_at,
            ]);

            $deleted++;
        }

        $this->info("Limpeza concluída: {$deleted} usuário(s) abandonados removidos.");

        return Command::SUCCESS;
    }
}
