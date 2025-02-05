@php
    use Carbon\Carbon;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>API Server</title>
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ asset('assets/vendors/feather/feather.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/ti-icons/css/themify-icons.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/typicons/typicons.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/simple-line-icons/css/simple-line-icons.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/font-awesome.min.css')}}">
  <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css')}}">
  <!-- Link CSS từ CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

  <!-- endinject -->
  <!-- End plugin css for this page -->
  <!-- plugin css for this page -->
  <link rel="stylesheet" href="{{asset('assets/vendors/jquery-toast-plugin/jquery.toast.min.css')}}">

  <link href="DataTables/datatables.min.css" rel="stylesheet">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{ asset('assets/css/vertical-layout-light/style.css')}}">
  <!-- endinject -->
  <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}" />
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">

  <style>
    .filter-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;

    }

    .filter-container input,
    .filter-container select {
        padding: 5px;
        border-radius: 4px;
        border: 1px solid #ccc;
        width: 150px;
        height: 30px;
        margin-left: 5px;
        margin-top: 10px;
    }
</style>
  @stack('third_party_stylesheets')

  @stack('page_css')

</head>
<body class="with-welcome-text">
  <div class="container-scroller">

    <!-- partial:partials/_navbar.html -->
    @include('admin.layouts.navbar')
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">

      <!-- partial:partials/_sidebar.html -->
      @include('admin.layouts.sidebar')
      <!-- partial -->
      <div class="main-panel">
        <div class="content-wrapper">
            @yield('content')
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        @include('admin.layouts.footer')
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->

  <!-- plugins:js -->
  <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js')}}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="{{ asset('assets/vendors/chart.js/Chart.min.js')}}"></script>
  <script src="{{ asset('assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
  <script src="{{ asset('assets/vendors/progressbar.js/progressbar.min.js')}}"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{ asset('assets/js/off-canvas.js')}}"></script>
  <script src="{{ asset('assets/js/hoverable-collapse.js')}}"></script>
  <script src="{{ asset('assets/js/template.js')}}"></script>
  <script src="{{ asset('assets/js/settings.js')}}"></script>
  <script src="{{ asset('assets/js/todolist.js')}}"></script>
  <script src="{{asset('assets/js/tooltips.js')}}"></script>
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{ asset('assets/js/jquery.cookie.js')}}" type="text/javascript"></script>

  <script src="{{ asset('assets/js/Chart.roundedBarCharts.js')}}"></script>
  <!-- End custom js for this page-->
  <!-- Plugin js for this page-->
  <script src="{{ asset('assets/vendors/jquery-toast-plugin/jquery.toast.min.js')}}"></script>
  <!-- Custom js for this page-->
  <script src="{{asset('assets/js/toastDemo.js')}}"></script>
  {{-- <script src="{{asset('assets/js/desktop-notification.js')}}"></script> --}}
  <!-- End custom js for this page-->
  <!-- End plugin js for this page-->
  <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="https://js.pusher.com/4.4/pusher.min.js"></script>
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


    <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <!-- Link JavaScript từ CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<!-- Link JavaScript ngôn ngữ từ CDN (Ví dụ: tiếng Việt) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.vi.min.js" charset="UTF-8"></script>
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>--}}

  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
  <script src="https://cdn.datatables.net/plug-ins/1.10.21/sorting/datetime-moment.js"></script>






  <script type="text/javascript">
        var pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
            encrypted: true,
            cluster: "ap1"
        });
        var channel = pusher.subscribe('NotificationEvent');
        channel.bind('send-message', function(data) {
            var createdAt = moment(data.created_at);
            var now = moment();
            var timeDiff = now.diff(createdAt, 'seconds');
            var timeAgo;
            if (timeDiff < 60) {
                timeAgo = timeDiff + ' giây trước';
            } else if (timeDiff < 3600) {
                timeAgo = Math.round(timeDiff / 60) + ' phút trước';
            } else if (timeDiff < 86400) {
                timeAgo = Math.round(timeDiff / 3600) + ' giờ trước';
            } else {
                timeAgo = Math.round(timeDiff / 86400) + ' ngày trước';
            }
            var newNotificationHtml = `
            <a class="dropdown-item preview-item py-3">
                <div class="preview-thumbnail">
                    <i class="mdi mdi-alert m-auto text-primary"></i>
                </div>
                <div class="preview-item-content">

                    <h6 class="preview-subject fw-normal text-dark mb-1"><i style="font-size: 8px; color:#1F3BB3" class="fa fa-circle"></i> ${data.title}</h6>
                    <p class="fw-light small-text mb-0 timestamp" data-timestamp="${data.created_at}">${timeAgo}</p>
                </div>
            </a>
            `;

            // add the new notification to the beginning of the notification list
            $('.content-notify').prepend(newNotificationHtml);

            // if the notification list has more than 5 items, remove the excess items from the end
            var notifications = $('.menu-notification .dropdown-item');
            if (notifications.length > 11) {
                notifications.slice(10, notifications.length-1).remove();
            }
            showNotifyToast(data.title,timeAgo);
            // Lấy phần tử có class là "count-notify"
            var countNotify = document.querySelector('.count-notify');

            // Lấy nội dung của phần tử đó
            var notifyCount = countNotify.innerText;

            // Chuyển đổi nội dung thành số nguyên
            var countInt = parseInt(notifyCount);

            // Thực hiện phép tính cộng thêm 1
            var newCount = countInt + 1;

            // Gán giá trị mới vào phần tử
            countNotify.innerText = newCount;
            var notificationDropdown = document.querySelector('#notificationDropdown');
            if (!notificationDropdown.querySelector('span.count')) {
            notificationDropdown.insertAdjacentHTML('beforeend', '<span class="count"></span>');
}
        });

        // move the view-more notification to the end of the list
        var viewMoreNotification = $('.menu-notification .view-more');
        $('.menu-notification').append(viewMoreNotification);
    </script>
    <script>
        setInterval(function() {
            var timestamps = document.getElementsByClassName("timestamp");

            for (var i = 0; i < timestamps.length; i++) {
                var timestamp = timestamps[i];
                var time = new Date(timestamp.getAttribute("data-timestamp"));
                var diff = Math.floor((new Date() - time) / 1000);
                var interval = 60;

                if (diff < 60) {
                    timestamp.textContent = diff + ' giây trước';
                } else if (diff < 3600) {
                    timestamp.textContent = Math.floor(diff / 60) + " phút trước";
                } else if (diff < 86400) {
                    timestamp.textContent = Math.floor(diff / 3600) + " giờ trước";
                } else {
                    timestamp.textContent = Math.floor(diff / 86400) + " ngày trước";
                }
            }
        }, 60000);
    </script>


@stack('third_party_scripts')

@stack('page_scripts')
</body>

</html>

