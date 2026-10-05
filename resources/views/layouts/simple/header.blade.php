<div class="page-header">
  <div class="header-wrapper row m-0">
    <div class="header-logo-wrapper col-auto p-0">
    <div class="logo-wrapper">
        <a href="{{ route('dashboard') }}">
            <img class="img-fluid" src="{{ asset('brand-logo.svg') }}" alt="DjamonoPay" style="width: 190px; height: auto;">
        </a>
    </div>
      <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i></div>
    </div>
    <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
      <ul class="nav-menus">
        @if (auth()->check())
          <li class="onhover-dropdown agent-notifications" data-agent-notifications
            data-user-id="{{ auth()->id() }}" data-endpoint="{{ route('agent.notifications.index') }}">
            <button type="button" class="notification-box" data-notification-toggle
              aria-expanded="false" aria-controls="agent-notification-panel">
              <svg>
                <use href="{{ asset('assets/svg/icon-sprite.svg#notification') }}"></use>
              </svg><span class="badge rounded-pill badge-secondary" data-notification-count hidden>0</span>
            </button>
            <div class="onhover-show-div notification-dropdown agent-notifications-panel"
              id="agent-notification-panel" data-notification-panel hidden>
              <h6 class="f-18 mb-0 dropdown-title">Notifications</h6>
              <p class="agent-notifications-status" data-notification-status role="status" aria-live="polite">
                Vos dernières opérations apparaîtront ici.
              </p>
              <button type="button" class="btn btn-sm btn-primary agent-notifications-enable" data-notification-enable>
                Activer le son et les alertes navigateur
              </button>
              <ul class="agent-notifications-list" data-notification-list aria-live="polite">
                <li data-notification-empty>Aucune nouvelle notification.</li>
              </ul>
            </div>
          </li>
        @else
          <li class="onhover-dropdown">
            <div class="notification-box">
              <svg>
                <use href="{{ asset('assets/svg/icon-sprite.svg#notification') }}"></use>
              </svg><span class="badge rounded-pill badge-secondary">4 </span>
            </div>
            <div class="onhover-show-div notification-dropdown">
              <h6 class="f-18 mb-0 dropdown-title">Notifications</h6>
              <ul>
                <li class="b-l-primary border-4">
                  <p>Delivery processing <span class="font-danger">10 min.</span></p>
                </li>
                <li class="b-l-success border-4">
                  <p>Order Complete<span class="font-success">1 hr</span></p>
                </li>
                <li class="b-l-secondary border-4">
                  <p>Tickets Generated<span class="font-secondary">3 hr</span></p>
                </li>
                <li class="b-l-warning border-4">
                  <p>Delivery Complete<span class="font-warning">6 hr</span></p>
                </li>
                <li><a class="f-w-700" href="#">Check all</a></li>
              </ul>
            </div>
          </li>
        @endif
        <li class="profile-nav onhover-dropdown pe-0 py-0">
          <div class="media profile-media">
            <span class="grid place-items-center rounded-circle text-white fw-bold" style="width:42px;height:42px;background:linear-gradient(145deg,#063f32,#00a878)">
              {{ mb_strtoupper(mb_substr(auth()->user()->prenom, 0, 1).mb_substr(auth()->user()->nom, 0, 1)) }}
            </span>
            <div class="media-body">
              <span>{{ auth()->user()->name }}</span>
              <p class="mb-0 font-roboto">{{ ucfirst(auth()->user()->role) }} <i class="middle fa fa-angle-down"></i></p>
            </div>
          </div>
          <ul class="profile-dropdown onhover-show-div">
            <li>
              <a href="{{ route('profile.show') }}">
                <i data-feather="user"></i><span>Voir mon profil</span>
              </a>
            </li>
            <li>
              <a href="{{ route('profile.show') }}#edit-profile">
                <i data-feather="edit-2"></i><span>Modifier mon profil</span>
              </a>
            </li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="border-0 bg-transparent p-0 text-start">
                  <i data-feather="log-out"></i><span>Déconnexion</span>
                </button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
    <script class="result-template" type="text/x-handlebars-template">
      <div class="ProfileCard u-cf">                        
      <div class="ProfileCard-avatar"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-airplay m-0"><path d="M5 17H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-1"></path><polygon points="12 15 17 21 7 21 12 15"></polygon></svg></div>
      <div class="ProfileCard-details">
      {{-- <div class="ProfileCard-realName">{{name}}</div> --}}
      </div>
      </div>
    </script>
    <script class="empty-template" type="text/x-handlebars-template"><div class="EmptyMessage">Your search turned up 0 results. This most likely means the backend is down, yikes!</div></script>
  </div>
</div>