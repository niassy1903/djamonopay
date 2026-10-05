<?php

use App\Enums\UserRole;
use App\Http\Controllers\AgentDashboardController;
use App\Http\Controllers\AgentNotificationController;
use App\Http\Controllers\TransactionCancellationRequestController;
use App\Http\Controllers\WalletDashboardController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/**
 * Route pour la page d'accueil.
 * Les utilisateurs connectés sont dirigés vers le tableau de bord de leur rôle.
 */
Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('/');

/**
 * Groupe de routes authentifiées sous le préfixe 'dashboard', avec accès filtré par rôle.
 */
Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/', function () {
        $route = UserRole::dashboardRoute(auth()->user()->role);

        abort_unless($route, 403);

        return redirect()->route($route);
    })->name('dashboard');

    Route::get('agent/notifications', [AgentNotificationController::class, 'index'])
        ->name('agent.notifications.index');
    Route::post('transactions/{transaction}/cancellation-requests', [TransactionCancellationRequestController::class, 'store'])
        ->name('transactions.cancellation-requests.store');

    Route::middleware('role:agent')->group(function () {
        Route::get('cancellation-requests', [TransactionCancellationRequestController::class, 'index'])
            ->name('agent.cancellation-requests.index');
        Route::post('cancellation-requests/{cancellationRequest}/approve', [TransactionCancellationRequestController::class, 'approve'])
            ->name('agent.cancellation-requests.approve');
        Route::post('cancellation-requests/{cancellationRequest}/reject', [TransactionCancellationRequestController::class, 'reject'])
            ->name('agent.cancellation-requests.reject');
        Route::get('index', [AgentDashboardController::class, 'index'])->name('index');
        Route::post('distributors/credit', [AgentDashboardController::class, 'creditDistributor'])->name('agent.distributors.credit');
        Route::post('users', [AgentDashboardController::class, 'storeUser'])->name('agent.users.store');
        Route::put('users/{users}', [AgentDashboardController::class, 'updateUser'])->name('agent.users.update');
        Route::delete('users/{users}', [AgentDashboardController::class, 'deleteUser'])->name('agent.users.destroy');
        Route::post('agent/transactions/{transaction}/cancel', [AgentDashboardController::class, 'cancelTransaction'])->name('agent.transactions.cancel');
        Route::get('dashboard-transactions', [AgentDashboardController::class, 'transactions'])->name('dashboard-transactions');
        Route::get('dashboard-activitées', [AgentDashboardController::class, 'activities'])->name('dashboard-activitées');
        Route::get('dashboard-utilisateurs', [AgentDashboardController::class, 'users'])->name('dashboard-utilisateurs');
    });

    Route::get('dashboard-distributeur', [WalletDashboardController::class, 'distributor'])
        ->middleware('role:distributeur')
        ->name('dashboard-distributeur');
    Route::get('dashboard-client', [WalletDashboardController::class, 'client'])
        ->middleware('role:client')
        ->name('dashboard-client');

    Route::middleware('role:client,distributeur')->group(function () {
        Route::get('payments/new', [WalletDashboardController::class, 'createPayment'])->name('payments.new');
        Route::post('payments', [WalletDashboardController::class, 'storePayment'])->name('payments.store');
        Route::post('client/transactions/{transaction}/cancel', [WalletDashboardController::class, 'cancelTransaction'])
            ->middleware('role:client')
            ->name('client.transactions.cancel');
        Route::get('recipients/lookup', [WalletDashboardController::class, 'lookupRecipient'])
            ->middleware('throttle:30,1')
            ->name('recipients.lookup');
    });
    Route::middleware('role:distributeur')->group(function () {
        Route::post('deposits', [WalletDashboardController::class, 'storeDeposit'])->name('distributor.deposits.store');
        Route::post('withdrawals/{transaction}/complete', [WalletDashboardController::class, 'completeWithdrawal'])->name('distributor.withdrawals.complete');
        Route::post('transactions/{transaction}/cancel', [WalletDashboardController::class, 'cancelTransaction'])->name('distributor.transactions.cancel');
    });
});

/**
 * Groupe de routes sous le préfixe 'others'.
 * Ces routes gèrent les erreurs spécifiques comme 400, 401, 403, 404, 500, et 503.
 * Chaque route renvoie une vue spécifique pour une erreur donnée. Par exemple,
 * '/others/404' affichera la vue 'errors.404' pour indiquer une erreur 404 (page non trouvée).
 * Chacune de ces routes a un nom comme 'error-404' pour faciliter leur appel via 'route('error-404')'.
 */
Route::prefix('others')->group(function () {
    Route::view('400', 'errors.400')->name('error-400');
    Route::view('401', 'errors.401')->name('error-401');
    Route::view('403', 'errors.403')->name('error-403');
    Route::view('404', 'errors.404')->name('error-404');
    Route::view('500', 'errors.500')->name('error-500');
    Route::view('503', 'errors.503')->name('error-503');
});

/**
 * Route pour effacer le cache.
 * Cette route exécute plusieurs commandes Artisan pour effacer différents types de caches (config, cache d'application, vues, routes).
 * Cela peut être utile après des modifications dans la configuration ou les routes pour s'assurer que les caches obsolètes ne sont pas utilisés.
 * Cette action POST est réservée aux agents et renvoie un message de confirmation.
 */
Route::post('/clear-cache', function () {
    Artisan::call('config:cache');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return 'Cache is cleared';
})->middleware(['auth', 'role:agent'])->name('clear.cache');
