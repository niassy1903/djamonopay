# Guide Tailwind CSS pour DjamonoPay - Thème Vert, Blanc, Gold

Ce guide explique comment utiliser le thème personnalisé Vert, Blanc, Gold pour les dashboards Agent, Distributeur et Client.

## 🎨 Palette de couleurs

### Couleurs Principales
- **Vert Primaires**: `#22c55e` (primary-500), `#16a34a` (primary-600), `#15803d` (primary-700)
- **Gold/Ambre**: `#f59e0b` (secondary-500), `#d97706` (secondary-600), `#b45309` (secondary-700)
- **Émeraude**: `#10b981` (accent-emerald-500), `#059669` (accent-emerald-600)
- **Blanc**: `#ffffff`

### Gradients Prédéfinis
- `bg-gradient-agent`: Vert clair → Vert moyen
- `bg-gradient-distributeur`: Gold clair → Gold moyen
- `bg-gradient-client`: Émeraude clair → Émeraude moyen
- `bg-gradient-primary`: Vert foncé → Vert très foncé
- `bg-gradient-gold`: Gold moyen → Gold foncé

## 📁 Configuration

Le fichier `tailwind.config.js` a été mis à jour avec:
- Nouvelle palette de couleurs personnalisée
- Ombres personnalisées pour chaque type de dashboard
- Dégradés personnalisés
- Animations supplémentaires

Le fichier `resources/css/djamonopay-styles.css` contient des classes utilitaires pour chaque type de dashboard.

## 🏗️ Utilisation dans les Vues Blade

### 1. Dashboard Agent

#### Layout Principal
```html
@extends('layouts.app')

@section('content')
<div class="dashboard-agent min-h-screen">
    <!-- En-tête -->
    <div class="dashboard-agent-header">
        <h1 class="dashboard-agent-title">Tableau de bord Agent</h1>
        <p class="dashboard-agent-subtitle">Gestion des transactions et utilisateurs</p>
    </div>
    
    <!-- Contenu -->
    <div class="dashboard-container">
        @yield('dashboard-content')
    </div>
</div>
@endsection
```

#### Cartes de Métriques
```html
<!-- Carte de métrique simple -->
<div class="card-agent">
    <div class="flex items-center">
        <div class="metric-agent-icon">
            <x-icon name="users" class="w-6 h-6" />
        </div>
        <div class="ml-4">
            <div class="metric-agent-value">1,234</div>
            <div class="metric-agent-label">Utilisateurs</div>
        </div>
    </div>
</div>

<!-- Grille de métriques -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="metric-agent">
        <div class="metric-agent-icon">
            <x-icon name="currency-dollar" class="w-6 h-6" />
        </div>
        <div class="metric-agent-value">56,789 FCFA</div>
        <div class="metric-agent-label">Revenu Total</div>
    </div>
    
    <div class="metric-agent">
        <div class="metric-agent-icon">
            <x-icon name="trending-up" class="w-6 h-6" />
        </div>
        <div class="metric-agent-value">+12.5%</div>
        <div class="metric-agent-label">Croissance</div>
    </div>
    
    <div class="metric-agent">
        <div class="metric-agent-icon">
            <x-icon name="credit-card" class="w-6 h-6" />
        </div>
        <div class="metric-agent-value">892</div>
        <div class="metric-agent-label">Transactions</div>
    </div>
    
    <div class="metric-agent">
        <div class="metric-agent-icon">
            <x-icon name="users" class="w-6 h-6" />
        </div>
        <div class="metric-agent-value">234</div>
        <div class="metric-agent-label">Clients Actifs</div>
    </div>
</div>
```

#### Tableaux
```html
<div class="card-agent">
    <div class="overflow-x-auto">
        <table class="table-agent w-full">
            <thead>
                <tr>
                    <th class="table-agent-header">Date</th>
                    <th class="table-agent-header">Montant</th>
                    <th class="table-agent-header">Statut</th>
                    <th class="table-agent-header">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                <tr class="table-agent-row">
                    <td class="table-agent-cell">{{ $transaction->date }}</td>
                    <td class="table-agent-cell">{{ $transaction->amount }} FCFA</td>
                    <td class="table-agent-cell">
                        <span class="badge-agent">Complété</span>
                    </td>
                    <td class="table-agent-cell">
                        <button class="btn-agent-primary text-sm">Voir</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
```

#### Boutons
```html
<!-- Bouton principal -->
<button class="btn-agent-primary">
    Ajouter un utilisateur
</button>

<!-- Bouton secondaire -->
<button class="btn-agent-secondary">
    Exporter
</button>

<!-- Bouton avec icône -->
<button class="btn-agent-primary flex items-center gap-2">
    <x-icon name="plus" class="w-5 h-5" />
    Nouvelle transaction
</button>
```

### 2. Dashboard Distributeur

#### Layout Principal
```html
@extends('layouts.app')

@section('content')
<div class="dashboard-distributeur min-h-screen">
    <!-- En-tête -->
    <div class="dashboard-distributeur-header">
        <h1 class="dashboard-distributeur-title">Tableau de bord Distributeur</h1>
        <p class="dashboard-distributeur-subtitle">Gestion des agents et commissions</p>
    </div>
    
    <!-- Contenu -->
    <div class="dashboard-container">
        @yield('dashboard-content')
    </div>
</div>
@endsection
```

#### Cartes de Métriques
```html
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="metric-distributeur">
        <div class="metric-distributeur-icon">
            <x-icon name="users" class="w-6 h-6" />
        </div>
        <div class="metric-distributeur-value">45</div>
        <div class="metric-distributeur-label">Agents Actifs</div>
    </div>
    
    <div class="metric-distributeur">
        <div class="metric-distributeur-icon">
            <x-icon name="currency-dollar" class="w-6 h-6" />
        </div>
        <div class="metric-distributeur-value">12,345 FCFA</div>
        <div class="metric-distributeur-label">Commissions</div>
    </div>
    
    <div class="metric-distributeur">
        <div class="metric-distributeur-icon">
            <x-icon name="chart-bar" class="w-6 h-6" />
        </div>
        <div class="metric-distributeur-value">2,345</div>
        <div class="metric-distributeur-label">Transactions Totales</div>
    </div>
</div>
```

#### Cartes de Distributeur
```html
<div class="card-distributeur">
    <h3 class="text-lg font-semibold text-secondary-800 mb-4">Commission du Mois</h3>
    <div class="flex items-center justify-between">
        <div>
            <p class="text-2xl font-bold text-secondary-700">8,900 FCFA</p>
            <p class="text-sm text-secondary-500">Moyenne: 2,225 FCFA/semaine</p>
        </div>
        <div class="w-16 h-16 bg-secondary-100 rounded-full flex items-center justify-center">
            <x-icon name="award" class="w-8 h-8 text-secondary-600" />
        </div>
    </div>
</div>
```

### 3. Dashboard Client

#### Layout Principal
```html
@extends('layouts.app')

@section('content')
<div class="dashboard-client min-h-screen">
    <!-- En-tête -->
    <div class="dashboard-client-header">
        <h1 class="dashboard-client-title">Tableau de bord Client</h1>
        <p class="dashboard-client-subtitle">Vos transactions et historique</p>
    </div>
    
    <!-- Contenu -->
    <div class="dashboard-container">
        @yield('dashboard-content')
    </div>
</div>
@endsection
```

#### Cartes de Métriques
```html
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="metric-client">
        <div class="metric-client-icon">
            <x-icon name="wallet" class="w-6 h-6" />
        </div>
        <div class="metric-client-value">25,000 FCFA</div>
        <div class="metric-client-label">Solde Actuel</div>
    </div>
    
    <div class="metric-client">
        <div class="metric-client-icon">
            <x-icon name="clock" class="w-6 h-6" />
        </div>
        <div class="metric-client-value">15</div>
        <div class="metric-client-label">Dernières Transactions</div>
    </div>
</div>
```

#### Historique des Transactions
```html
<div class="card-client">
    <h3 class="text-lg font-semibold text-accent-emerald-800 mb-4">Historique des Transactions</h3>
    
    @foreach($transactions as $transaction)
    <div class="flex items-center justify-between p-4 border-b border-accent-emerald-100 last:border-b-0">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-accent-emerald-100 rounded-full flex items-center justify-center mr-4">
                @if($transaction->type === 'deposit')
                    <x-icon name="arrow-down" class="w-5 h-5 text-accent-emerald-600" />
                @else
                    <x-icon name="arrow-up" class="w-5 h-5 text-accent-emerald-600" />
                @endif
            </div>
            <div>
                <p class="font-medium text-accent-emerald-800">{{ $transaction->description }}</p>
                <p class="text-sm text-accent-emerald-500">{{ $transaction->date }}</p>
            </div>
        </div>
        <div class="text-right">
            <p class="font-semibold 
                @if($transaction->type === 'deposit') text-green-600 
                @else text-red-600 @endif">
                {{ $transaction->amount }} FCFA
            </p>
            <p class="text-sm text-accent-emerald-500">{{ $transaction->status }}</p>
        </div>
    </div>
    @endforeach
</div>
```

## 🎯 Classes Utilitaires Personnalisées

### Classes pour Agent
- `.dashboard-agent` - Fond de dashboard
- `.card-agent` - Carte de base
- `.dashboard-agent-header` - En-tête du dashboard
- `.dashboard-agent-title` - Titre
- `.dashboard-agent-subtitle` - Sous-titre
- `.btn-agent-primary` - Bouton principal
- `.btn-agent-secondary` - Bouton secondaire
- `.metric-agent` - Carte de métrique
- `.metric-agent-value` - Valeur de métrique
- `.metric-agent-label` - Label de métrique
- `.metric-agent-icon` - Icône de métrique
- `.table-agent` - Tableau
- `.table-agent-header` - En-tête de tableau
- `.table-agent-row` - Ligne de tableau
- `.table-agent-cell` - Cellule de tableau
- `.badge-agent` - Badge

### Classes pour Distributeur
- `.dashboard-distributeur` - Fond de dashboard
- `.card-distributeur` - Carte de base
- `.dashboard-distributeur-header` - En-tête
- `.dashboard-distributeur-title` - Titre
- `.dashboard-distributeur-subtitle` - Sous-titre
- `.btn-distributeur-primary` - Bouton principal
- `.btn-distributeur-secondary` - Bouton secondaire
- `.metric-distributeur` - Carte de métrique
- `.metric-distributeur-value` - Valeur
- `.metric-distributeur-label` - Label
- `.metric-distributeur-icon` - Icône
- `.table-distributeur` - Tableau
- `.badge-distributeur` - Badge

### Classes pour Client
- `.dashboard-client` - Fond de dashboard
- `.card-client` - Carte de base
- `.dashboard-client-header` - En-tête
- `.dashboard-client-title` - Titre
- `.dashboard-client-subtitle` - Sous-titre
- `.btn-client-primary` - Bouton principal
- `.btn-client-secondary` - Bouton secondaire
- `.metric-client` - Carte de métrique
- `.metric-client-value` - Valeur
- `.metric-client-label` - Label
- `.metric-client-icon` - Icône
- `.table-client` - Tableau
- `.badge-client` - Badge

## 🎨 Combinaisons de Couleurs Recommandées

### Pour les Graphiques
```html
<!-- Graphique pour Agent -->
<div class="chart-container-agent">
    <!-- Contenu du graphique -->
    @push('scripts')
    <script>
        // Configuration des couleurs pour les graphiques
        const agentColors = {
            primary: '#22c55e',
            secondary: '#16a34a',
            success: '#4ade80',
            info: '#86efac',
            warning: '#fbbf24',
        };
    </script>
    @endpush
</div>
```

### Pour les Alertes
```html
<!-- Alerte Agent -->
<div class="alert-agent">
    <h4 class="font-semibold mb-2">✅ Succès</h4>
    <p>Votre transaction a été complétée avec succès.</p>
</div>

<!-- Alerte Distributeur -->
<div class="alert-distributeur">
    <h4 class="font-semibold mb-2">ℹ️ Information</h4>
    <p>Nouvel agent inscrit dans votre réseau.</p>
</div>

<!-- Alerte Client -->
<div class="alert-client">
    <h4 class="font-semibold mb-2">✅ Confirmation</h4>
    <p>Votre paiement a été reçu.</p>
</div>
```

## 📱 Responsive Design

Toutes les classes sont conçues pour être responsive. Exemple:
```html
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    <!-- Les cartes s'adapteront automatiquement -->
    <div class="card-agent">...</div>
    <div class="card-agent">...</div>
    <div class="card-agent">...</div>
</div>
```

## 🎭 Animations

### Animations disponibles
- `.float` - Animation de flottement
- `.pulse-soft` - Pulsation douce
- `.shimmer` - Effet de scintillement

### Exemple d'utilisation
```html
<div class="card-agent float">
    <!-- Carte avec effet de flottement -->
</div>

<div class="metric-agent-value pulse-soft">
    <!-- Valeur avec pulsation -->
    1,234
</div>

<div class="shimmer h-4 w-32">
    <!-- Placeholder avec effet shimmer -->
</div>
```

## 🔧 Intégration avec les Vues Existantes

Pour intégrer ces styles dans vos vues existantes:

1. **Importer le CSS** (dans votre layout principal):
```html
<head>
    <!-- Autres imports -->
    <link href="{{ asset('css/djamonopay-styles.css') }}" rel="stylesheet">
</head>
```

2. **Utiliser les classes**:
```html
<!-- Au lieu de -->
<div class="bg-blue-500 text-white">...</div>

<!-- Utilisez -->
<div class="btn-agent-primary">...</div>
```

3. **Personnaliser selon le type d'utilisateur**:
```php
@php
    $userType = auth()->user()->role;
    $dashboardClass = 'dashboard-' . strtolower($userType);
    $cardClass = 'card-' . strtolower($userType);
    $btnPrimaryClass = 'btn-' . strtolower($userType) . '-primary';
    $btnSecondaryClass = 'btn-' . strtolower($userType) . '-secondary';
@endphp

<div class="{{ $dashboardClass }}">
    <button class="{{ $btnPrimaryClass }}">Action</button>
    <div class="{{ $cardClass }}">...</div>
</div>
```

## 🌟 Bonnes Pratiques

1. **Cohérence**: Utilisez les classes personnalisées pour maintenir la cohérence
2. **Accessibilité**: Les couleurs choisies respectent les contrastes WCAG
3. **Performance**: Les animations sont optimisées pour ne pas impacter les performances
4. **Maintenabilité**: Utilisez le système de design pour faciliter les futures mises à jour

## 🎯 Personnalisation

Pour modifier les couleurs ou les styles:

1. **Modifier `tailwind.config.js`**:
```javascript
// Dans la section theme.extend.colors
primary: {
    500: '#votre_couleur_vert',
    600: '#votre_couleur_vert_fonce',
    // ...
}
```

2. **Ajouter de nouvelles classes**:
Ajoutez de nouvelles classes dans `djamonopay-styles.css` en suivant le même pattern.

3. **Tester les modifications**:
```bash
npm run dev
# ou
npm run build
```

---

**Dernière mise à jour**: 3 Octobre 2026  
**Version**: 1.0.0  
**Auteur**: DjamonoPay Development Team