<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <li class="nav-item nav-category">CRM</li>
      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-toggle="collapse" href="#orders" aria-expanded="false" aria-controls="orders">
          <i class="menu-icon mdi mdi-clipboard-text"></i>
          <span class="menu-title">Orders</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="orders" style="">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="{{asset('orders')}}">Tất cả</a></li>
            <li class="nav-item"><a class="nav-link" href="
                {{-- {{asset('orders?orderStatus=Success%20Payment')}} --}}
                ">Success Payment</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="
                {{-- {{asset('orders?orderStatus=Label%20Created')}} --}}
                ">Label Created</a></li>
            <li class="nav-item"><a class="nav-link" href="
                {{-- {{asset('orders?orderStatus=In%20Transit')}} --}}
                ">In Transit</a></li>
            <li class="nav-item"><a class="nav-link" href="
                {{-- {{asset('orders?orderStatus=Delivered')}} --}}
                ">Delivered</a></li>
          </ul>
        </div>
      </li>

      <li class="nav-item nav-category">HISTORY</li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#history" aria-expanded="false" aria-controls="history">
          <i class="menu-icon mdi mdi-history"></i>
          <span class="menu-title">Lịch sử hoạt động</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="history">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="{{asset('history')}}"> Tất cả </a></li>
            <li class="nav-item visible"> <a class="nav-link" href="
                {{-- {{asset('history')}} --}}
                "> Create </a></li>
            <li class="nav-item"> <a class="nav-link" href="
                {{-- {{asset('history')}} --}}
                "> Update </a></li>
            <li class="nav-item"> <a class="nav-link" href="
                {{-- {{asset('history')}} --}}
                "> Delete </a></li>
          </ul>
        </div>
      </li>
      <li class="nav-item nav-category">Hệ Thống</li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#setting" aria-expanded="false" aria-controls="setting">
          <i class="menu-icon mdi mdi-settings"></i>
          <span class="menu-title">Cài Đặt Chung</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="setting">
          <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="{{route("api-1office.index")}}">Cấu hình API 1Office</a></li>
              <li class="nav-item"> <a class="nav-link" href="{{route("alerts.index")}}">Alert</a></li>
            {{-- <li class="nav-item"> <a class="nav-link" href="#">Webhook</a></li>
            <li class="nav-item"> <a class="nav-link" href="#">notification</a></li>
            <li class="nav-item"> <a class="nav-link" href="#">Tasks</a></li> --}}
            <li class="nav-item"> <a class="nav-link" href="#">Khác</a></li>
          </ul>
        </div>
      </li>

    </ul>
  </nav>
