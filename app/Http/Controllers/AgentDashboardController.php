<?php

namespace App\Http\Controllers;

use App\Actions\CancelWalletTransaction;
use App\Enums\UserRole;
use App\Http\Requests\Users\StoreUsersRequest;
use App\Http\Requests\Users\UpdateUsersRequest;
use App\Models\Compte;
use App\Models\SystemLogger;
use App\Models\Transaction;
use App\Models\Users2;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AgentDashboardController extends Controller
{
    public function index(): View
    {
        $transactions = Transaction::with(['compteSource.user', 'compteDestination.user']);
        $businessTransactions = Transaction::whereNotIn('type', ['bonus_distributeur', 'annulation_bonus', 'remboursement_annulation']);
        $successfulTransactions = (clone $businessTransactions)->where('statut', 'terminee')->where('devise', 'XOF');

        return view('dashboard.index', [
            'userCounts' => Users2::query()
                ->select('role', DB::raw('count(*) as total'))
                ->groupBy('role')
                ->pluck('total', 'role'),
            'activeAccounts' => Compte::where('statut', 'actif')->count(),
            'totalBalance' => Compte::where('statut', 'actif')->where('devise', 'XOF')->sum('solde'),
            'transactionCount' => $businessTransactions->count(),
            'successfulAmount' => (clone $successfulTransactions)->sum('montant'),
            'pendingCount' => (clone $businessTransactions)->where('statut', 'en_attente')->count(),
            'recentTransactions' => $transactions->latest()->limit(8)->get(),
            'recentActivities' => SystemLogger::with('user')->latest()->limit(6)->get(),
            'monthlyChart' => $this->monthlyTransactions(),
            'distributors' => Users2::where('role', UserRole::DISTRIBUTEUR)->where('etat_compte', true)
                ->with('comptes')
                ->orderBy('nom')
                ->get(),
            'creditToken' => (string) Str::uuid(),
            'clientTransactions' => Transaction::with(['compteSource.user', 'compteDestination.user'])
                ->whereIn('statut', ['terminee', 'en_attente'])
                ->where(function ($query): void {
                    $query->whereHas('compteSource.user', fn ($users) => $users->where('role', UserRole::CLIENT))
                        ->orWhereHas('compteDestination.user', fn ($users) => $users->where('role', UserRole::CLIENT));
                })
                ->whereNotIn('type', ['bonus_distributeur', 'annulation_bonus', 'remboursement_annulation'])
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }

    public function creditDistributor(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'distributor_account' => ['required', 'integer', 'exists:comptes,id'],
            'amount' => ['required', 'integer', 'min:1', 'max:1000000000'],
            'credit_token' => ['required', 'uuid'],
        ]);

        $amount = (int) $data['amount'];
        $account = DB::transaction(function () use ($request, $data, $amount): Compte {
            $account = Compte::query()->lockForUpdate()->findOrFail($data['distributor_account']);
            $account->load('user');

            if ($account->devise !== 'XOF'
                || $account->statut !== 'actif'
                || $account->user?->role !== UserRole::DISTRIBUTEUR
                || ! $account->user->etat_compte) {
                throw ValidationException::withMessages([
                    'distributor_account' => 'Le compte distributeur est inactif ou invalide.',
                ]);
            }

            $existing = Transaction::where('idempotency_key', $data['credit_token'])->first();
            if ($existing) {
                if ((int) $existing->compte_destination_id !== (int) $account->id
                    || (int) $existing->montant !== $amount
                    || $existing->type !== 'credit_agent') {
                    throw ValidationException::withMessages(['credit_token' => 'Cette confirmation a déjà été utilisée.']);
                }

                return $account;
            }

            $account->solde = round((float) $account->solde + $amount, 2);
            $account->save();

            $credit = Transaction::create([
                'reference' => 'AGT-'.strtoupper((string) Str::ulid()),
                'idempotency_key' => $data['credit_token'],
                'actor_id' => $request->user()->id,
                'processed_by_user_id' => $request->user()->id,
                'compte_destination_id' => $account->id,
                'type' => 'credit_agent',
                'montant' => $amount,
                'frais' => 0,
                'devise' => 'XOF',
                'statut' => 'terminee',
                'description' => 'Crédit de trésorerie agent au distributeur.',
                'traitee_at' => now(),
            ]);

            SystemLogger::create([
                'user_id' => $request->user()->id,
                'action' => 'wallet.distributor.credited',
                'description' => 'Crédit de '.$amount.' XOF au distributeur '.$account->numero_compte.'.',
                'adresse_ip' => $request->ip(),
                'subject_type' => Transaction::class,
                'subject_id' => $credit->id,
            ]);

            return $account;
        }, 3);

        return redirect()->route('index')->with('operation_message', 'Le compte distributeur a été crédité.');
    }

    public function cancelTransaction(
        Request $request,
        Transaction $transaction,
        CancelWalletTransaction $cancelWalletTransaction
    ): RedirectResponse {
        $cancelWalletTransaction->execute($transaction, $request->user(), $request);

        return back()->with('operation_message', 'La transaction a été annulée et ses soldes restaurés.');
    }

    public function transactions(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'type' => ['nullable', 'string', 'in:credit_agent,depot,retrait,paiement_qr,bonus_distributeur,annulation_bonus,remboursement_annulation'],
            'status' => ['nullable', 'string', 'in:terminee,en_attente,echouee,annulee'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $query = Transaction::with(['compteSource.user', 'compteDestination.user']);
        $this->applyTransactionFilters($query, $filters);

        return view('dashboard.dashboard-transactions', [
            'transactions' => $query->latest()->paginate(15)->appends($filters),
            'transactionCount' => Transaction::count(),
            'successfulCount' => Transaction::where('statut', 'terminee')->count(),
            'pendingCount' => Transaction::where('statut', 'en_attente')->count(),
            'failedCount' => Transaction::whereIn('statut', ['echouee', 'annulee'])->count(),
            'totalAmount' => Transaction::where('statut', 'terminee')->where('devise', 'XOF')->sum('montant'),
        ]);
    }

    public function activities(): View
    {
        $filters = request()->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);
        $query = SystemLogger::with('user');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($activities) use ($search): void {
                $activities->where('action', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('adresse_ip', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($user) use ($search): void {
                        $user->where('prenom', 'like', "%{$search}%")
                            ->orWhere('nom', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if (! empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return view('dashboard.dashboard-activitées', [
            'activities' => $query->latest()->paginate(20)->appends($filters),
            'activityCount' => SystemLogger::count(),
        ]);
    }

    public function users(Request $request): View
    {
        $filters = $request->validate([
            'role' => ['nullable', 'string', 'in:client,distributeur,agent'],
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ]);

        $query = Users2::query()
            ->withSum('comptesXof as solde_total', 'solde')
            ->with('comptesXof')
            ->latest();

        if (! empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $fullNameExpression = DB::connection()->getDriverName() === 'mysql'
                ? "CONCAT(prenom, ' ', nom)"
                : "prenom || ' ' || nom";
            $query->where(function ($users) use ($search, $fullNameExpression): void {
                $users->where('prenom', 'like', "%{$search}%")
                    ->orWhere('nom', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telephone', 'like', "%{$search}%")
                    ->orWhereHas('comptesXof', fn ($accounts) => $accounts->where('numero_compte', 'like', "%{$search}%"));
                $users->orWhereRaw($fullNameExpression.' LIKE ?', ["%{$search}%"]);
            });
        }

        if (! empty($filters['status'])) {
            $query->where('etat_compte', $filters['status'] === 'active');
        }

        return view('dashboard.dashboard-utilisateurs', [
            'users' => $query->paginate(15)->appends($filters),
            'clientCount' => Users2::where('role', UserRole::CLIENT)->count(),
            'distributorCount' => Users2::where('role', UserRole::DISTRIBUTEUR)->count(),
            'agentCount' => Users2::where('role', UserRole::AGENT)->count(),
            'activeUserCount' => Users2::where('etat_compte', true)->count(),
        ]);
    }

    public function storeUser(StoreUsersRequest $request): RedirectResponse
    {
        $user = DB::transaction(function () use ($request): Users2 {
            $user = Users2::create($request->validated());
            SystemLogger::create([
                'user_id' => $request->user()->id,
                'action' => 'user.created',
                'description' => 'Création du compte '.$user->role.' '.$user->email.'.',
                'adresse_ip' => $request->ip(),
                'subject_type' => Users2::class,
                'subject_id' => $user->id,
            ]);

            return $user;
        }, 3);

        return redirect()->route('dashboard-utilisateurs')
            ->with('operation_message', 'Le compte '.$user->email.' a été créé avec succès.');
    }

    public function updateUser(UpdateUsersRequest $request, Users2 $users): RedirectResponse
    {
        abort_if((int) $request->user()->id === (int) $users->id, 422, 'Vous ne pouvez pas modifier votre propre compte depuis cette page.');

        $data = $request->validated();
        if (empty($data['mot_de_passe'])) {
            unset($data['mot_de_passe']);
        }

        DB::transaction(function () use ($request, $users, $data): void {
            $users->update($data);
            SystemLogger::create([
                'user_id' => $request->user()->id,
                'action' => 'user.updated',
                'description' => 'Modification du compte '.$users->email.'.',
                'adresse_ip' => $request->ip(),
                'subject_type' => Users2::class,
                'subject_id' => $users->id,
            ]);
        }, 3);

        return redirect()->route('dashboard-utilisateurs')
            ->with('operation_message', 'Le compte '.$users->email.' a été mis à jour.');
    }

    public function deleteUser(Request $request, Users2 $users): RedirectResponse
    {
        abort_if((int) $request->user()->id === (int) $users->id, 422, 'Vous ne pouvez pas supprimer votre propre compte.');

        DB::transaction(function () use ($request, $users): void {
            $accountIds = $users->comptes()->pluck('id');
            $hasTransactions = Transaction::query()
                ->whereIn('compte_source_id', $accountIds)
                ->orWhereIn('compte_destination_id', $accountIds)
                ->exists();
            $hasBalance = $users->comptes()->where('solde', '!=', 0)->exists();

            if ($hasTransactions || $hasBalance) {
                throw ValidationException::withMessages([
                    'user' => 'Suppression impossible : cet utilisateur possède un solde ou un historique financier. Désactivez son compte dans Modifier pour préserver les données.',
                ]);
            }

            SystemLogger::create([
                'user_id' => $request->user()->id,
                'action' => 'user.deleted',
                'description' => 'Suppression du compte '.$users->email.'.',
                'adresse_ip' => $request->ip(),
                'subject_type' => Users2::class,
                'subject_id' => $users->id,
            ]);
            $users->delete();
        }, 3);

        return redirect()->route('dashboard-utilisateurs')
            ->with('operation_message', 'Le compte a été supprimé.');
    }

    /**
     * @param  array{search?: string, type?: string, status?: string, start_date?: string, end_date?: string}  $filters
     */
    private function applyTransactionFilters($query, array $filters): void
    {
        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['status'])) {
            $query->where('statut', $filters['status']);
        }

        if (! empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (! empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($transactions) use ($search): void {
                $transactions->where('reference', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('compteSource', fn ($account) => $account->where('numero_compte', 'like', "%{$search}%"))
                    ->orWhereHas('compteDestination', fn ($account) => $account->where('numero_compte', 'like', "%{$search}%"))
                    ->orWhereHas('compteSource.user', function ($user) use ($search): void {
                        $user->where('prenom', 'like', "%{$search}%")
                            ->orWhere('nom', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('compteDestination.user', function ($user) use ($search): void {
                        $user->where('prenom', 'like', "%{$search}%")
                            ->orWhere('nom', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%");
                    });
            });
        }
    }

    private function monthlyTransactions(): array
    {
        $start = now()->startOfMonth()->subMonths(5);
        $monthExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql' => "to_char(created_at, 'YYYY-MM')",
            default => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $results = Transaction::query()
            ->selectRaw($monthExpression.' as month')
            ->selectRaw('COUNT(*) as count, SUM(CASE WHEN statut = ? THEN montant ELSE 0 END) as volume', ['terminee'])
            ->where('created_at', '>=', $start)
            ->where('devise', 'XOF')
            ->whereNotIn('type', ['bonus_distributeur', 'annulation_bonus', 'remboursement_annulation'])
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $labels = [];
        $counts = [];
        $volumes = [];

        for ($offset = 0; $offset < 6; $offset++) {
            $month = $start->copy()->addMonths($offset);
            $row = $results->get($month->format('Y-m'));
            $labels[] = $month->format('m/Y');
            $counts[] = (int) ($row?->count ?? 0);
            $volumes[] = (float) ($row?->volume ?? 0);
        }

        return [
            'labels' => $labels,
            'series' => [
                ['name' => 'Transactions', 'type' => 'column', 'data' => $counts],
                ['name' => 'Volume terminé (XOF)', 'type' => 'line', 'data' => $volumes],
            ],
        ];
    }
}
