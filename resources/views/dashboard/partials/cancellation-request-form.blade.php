<div class="w-full rounded-xl border border-amber-200 bg-amber-50 p-4">
    @if ($cancellationRequest?->status === 'pending')
        <p class="text-sm font-semibold text-amber-900">Demande transmise à l’assistance Djamanopay, en attente de vérification.</p>
    @else
        @if ($cancellationRequest?->status === 'rejected')
            <p class="mb-3 text-sm font-semibold text-rose-800">Votre demande précédente a été refusée. Vous pouvez la soumettre à nouveau avec les informations exactes ou contacter l’assistance Djamanopay.</p>
        @endif
        <p class="mb-3 text-sm text-amber-900">Le délai d’annulation directe de 15 minutes est dépassé. Confirmez les informations exactes de la transaction ; l’assistance les vérifiera avant toute action.</p>
        <form method="POST" action="{{ route('transactions.cancellation-requests.store', $transaction) }}" class="grid gap-3 sm:grid-cols-2">
            @csrf
            <label class="text-sm font-medium text-slate-700">
                Référence exacte
                <input name="reference" value="{{ old('reference') }}" required maxlength="120" class="mt-1 block w-full rounded-lg border border-amber-200 px-3 py-2">
                @error('reference') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
            </label>
            <label class="text-sm font-medium text-slate-700">
                Montant exact ({{ $transaction->devise }})
                <input name="amount" type="number" min="0.01" step="0.01" value="{{ old('amount') }}" required class="mt-1 block w-full rounded-lg border border-amber-200 px-3 py-2">
                @error('amount') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
            </label>
            <label class="text-sm font-medium text-slate-700">
                Votre téléphone enregistré
                <input name="phone" type="tel" value="{{ old('phone') }}" required minlength="6" maxlength="30" autocomplete="tel" class="mt-1 block w-full rounded-lg border border-amber-200 px-3 py-2">
                @error('phone') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
            </label>
            <label class="text-sm font-medium text-slate-700">
                Motif de la demande
                <input name="reason" value="{{ old('reason') }}" required minlength="10" maxlength="1000" class="mt-1 block w-full rounded-lg border border-amber-200 px-3 py-2">
                @error('reason') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
            </label>
            @error('transaction') <p class="text-sm text-rose-600 sm:col-span-2">{{ $message }}</p> @enderror
            <button type="submit" class="rounded-lg bg-amber-500 px-4 py-2 text-sm font-bold text-slate-900 hover:bg-amber-400 sm:col-span-2">Envoyer à l’assistance pour vérification</button>
        </form>
    @endif
</div>
