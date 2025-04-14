<aside class="left-sidebar" data-sidebarbg="skin6">
    <div class="scroll-sidebar">
      <nav class="sidebar-nav">
        <ul id="sidebarnav">
          <li class="sidebar-item {{ request()->is('/') ? 'selected' : '' }}">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('dashboard') }}" aria-expanded="false">
              <i class="mdi mdi-view-dashboard"></i>
              <span class="hide-menu">Dashboard</span>
            </a>
          </li>
          <li class="sidebar-item {{ request()->segment(1) === 'product' ? 'selected' : '' }}">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('product.home') }}" aria-expanded="false">
              <i class="mdi mdi-store"></i>
              <span class="hide-menu">Produk</span>
            </a>
          </li>
          <li class="sidebar-item {{ request()->segment(index: 1) === 'transaction' ? 'selected' : '' }}">
            <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('transaction.home') }}" aria-expanded="false">
              <i class="mdi mdi-cart"></i>
              <span class="hide-menu">Pembelian</span>
            </a>
          </li>
          @if (Auth::user()->role === 'admin')
            <li class="sidebar-item {{ request()->segment(1) === 'user' ? 'selected' : '' }}">
              <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('user.home') }}" aria-expanded="false">
                <i class="mdi mdi-account"></i>
                <span class="hide-menu">User</span>
              </a>
            </li>
          @endif
        </ul>
      </nav>
    </div>
</aside>