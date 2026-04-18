<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token()}}">
  <title>Painel Admisnistrativo </title>

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('backend/assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/assets/modules/fontawesome/css/all.min.css') }}">

  <!-- CSS Libraries -->
  <link rel="stylesheet" href="{{ asset('backend/assets/modules/jqvmap/dist/jqvmap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/assets/modules/weather-icon/css/weather-icons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/assets/modules/weather-icon/css/weather-icons-wind.min.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/assets/modules/summernote/summernote-bs4.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/assets/modules/jquery-selectric/selectric.css') }}">

  <!-- CSS DATATABLE -->
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.6/css/dataTables.dataTables.min.css'>
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.6/css/dataTables.bootstrap5.css'>


  <!-- CSS Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css') }}">

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/assets/css/components.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">

  <!-- Favicons -->
	<link rel="icon" type="{{ asset('backend/assets/image/.png') }}" href="{{ asset('backend/assets/icon/favicon-32x32.png') }}" sizes="32x32">
	<link rel="apple-touch-icon" href="{{ asset('backend/assets/icon/favicon-32x32.png') }}">
	<link rel="apple-touch-icon" sizes="72x72" href="{{ asset('backend/assets/icon/apple-touch-icon-72x72.png') }}">
	<link rel="apple-touch-icon" sizes="114x114" href="{{ asset('backend/assets/icon/apple-touch-icon-114x114.png') }}">
	<link rel="apple-touch-icon" sizes="144x144" href="{{ asset('backend/assets/icon/apple-touch-icon-144x144.png') }}">

  <!-- ICONES -->
  <link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap-iconpicker.min.css') }}">
<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script>
<!-- /END GA --></head>

<body>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class=""></div>
      <!-- START NAVABAR - MAYKONSILVEIRA.COM.BR -->
      @include('admin.layouts.navbar')

      <!-- END NAVBAR - MAYKONSILVEIRA.COM.BR -->

      <!-- START SIDEBAR - MAYKONSILVEIRA.COM.BR -->
      @include('admin.layouts.sidebar')

      <!-- END SIDEBAR - MAYKONSILVEIRA.COM.BR -->

      <!-- START MAIN CONTENT - MAYKONSILVEIRA.COM.BR -->
      <div class="main-content">

        @yield('content')

      </div>
      <!-- END MAIN CONTENT - MAYKONSILVEIRA.COM.BR -->

      <!-- START FOOTER - MAYKONSILVEIRA.COM.BR -->
      <footer class="main-footer">
        <div class="footer-left">
          Todos os Direitos Reservados &copy; @date('Y') <div class="bullet"></div> Desenvolvido Thiago Cruz Ferreira De Melo Versão 1.0
        </div>
        <div class="footer-right">

        </div>
      </footer>
      <!-- END FOOTER - MAYKONSILVEIRA.COM.BR -->
    </div>
  </div>

  <!-- General JS Scripts -->
  <script src="{{ asset('backend/assets/modules/jquery.min.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/popper.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/tooltip.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/moment.min.js') }}"></script>
  <script src="{{ asset('backend/assets/js/stisla.js') }}"></script>

  <!-- JS Libraies -->
  <script src="{{ asset('backend/assets/modules/simple-weather/jquery.simpleWeather.min.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/chart.min.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/jqvmap/dist/jquery.vmap.min.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/summernote/summernote-bs4.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/upload-preview/{{assets/js/jquery.uploadPreview.min.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
  <script src="{{ asset('backend/assets/js/page/features-post-create.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/jquery-selectric/jquery.selectric.min.js') }}"></script>

  <!-- JS DATATABLE -->
 <script src="https://cdn.datatables.net/2.0.6/js/dataTables.min.js"></script>
 <script src="https://cdn.datatables.net/2.0.6/js/dataTables.bootstrap5.js"></script>

  <!-- JS SWEET -->
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- JS Toastr -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <!-- ICONES -->
  <script src="{{ asset('backend/assets/js/bootstrap-iconpicker.bundle.min.js') }}"></script>


  <!-- Page Specific JS File -->
  <script src="{{ asset('backend/assets/js/page/index-0.js') }}"></script>

  <!-- Template JS File -->
  <script src="{{ asset('backend/assets/js/scripts.js') }}"></script>
  <script src="{{ asset('backend/assets/js/custom.js') }}"></script>
  <script src="{{ asset('backend/assets/js/jmask.js') }}"></script>

  <script>
    @if($errors->any())
      (function () {
        var validationErrors = @json($errors->all());

        validationErrors.forEach(function (errorMessage) {
          toastr.error(errorMessage);
        });
      })();
    @endif
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (!window.localStorage) {
        return;
      }

      Object.keys(window.localStorage).forEach(function (key) {
        if (/^admin\.(form-draft\.|settings\.|agendamentos\.|patients\.)/.test(key)) {
          window.localStorage.removeItem(key);
        }
      });

      Array.from(document.querySelectorAll('form[data-draft-form="true"]')).forEach(function (form) {
        form.setAttribute('autocomplete', 'off');

        Array.from(form.querySelectorAll('input[name], select[name], textarea[name]')).forEach(function (field) {
          if (field.type !== 'hidden') {
            field.setAttribute('autocomplete', 'off');
          }
        });
      });

      if (window.sessionStorage) {
        window.sessionStorage.removeItem('admin.form-draft.last-submitted-key');
      }
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var duplicateCheckForms = Array.from(document.querySelectorAll('form[data-patient-live-check="true"]'));

      if (!duplicateCheckForms.length || !window.fetch) {
        return;
      }

      duplicateCheckForms.forEach(function (form) {
        var endpoint = form.getAttribute('data-patient-duplicate-url');
        var patientId = form.getAttribute('data-patient-id') || '';
        var submitButtons = Array.from(form.querySelectorAll('button[type="submit"]'));
        var watchedFields = ['cpf', 'email', 'telefone']
          .map(function (name) {
            return form.querySelector('[name="' + name + '"]');
          })
          .filter(Boolean);

        var debounceTimer = null;
        var activeConflicts = {};

        if (!endpoint || !watchedFields.length) {
          return;
        }

        function ensureFeedbackElement(field) {
          var container = field.closest('.form-group');
          if (!container) {
            return null;
          }

          var feedback = container.querySelector('.patient-live-conflict-feedback');
          if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'text-danger small mt-1 patient-live-conflict-feedback';
            container.appendChild(feedback);
          }

          return feedback;
        }

        function updateSubmitState() {
          var hasConflicts = Object.values(activeConflicts).some(function (message) {
            return Boolean(message);
          });

          submitButtons.forEach(function (button) {
            button.disabled = hasConflicts;
          });
        }

        function updateFieldState(field, message) {
          var feedback = ensureFeedbackElement(field);

          activeConflicts[field.name] = message || '';
          field.classList.toggle('is-invalid', Boolean(message));

          if (feedback) {
            feedback.textContent = message || '';
          }

          updateSubmitState();
        }

        function collectParams() {
          var params = new URLSearchParams();

          watchedFields.forEach(function (field) {
            params.set(field.name, field.value || '');
          });

          if (patientId) {
            params.set('patient_id', patientId);
          }

          return params;
        }

        function runDuplicateCheck() {
          var params = collectParams();

          fetch(endpoint + '?' + params.toString(), {
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json'
            }
          })
            .then(function (response) {
              if (!response.ok) {
                throw new Error('Falha ao validar duplicidade');
              }

              return response.json();
            })
            .then(function (data) {
              var conflicts = data.conflicts || {};

              watchedFields.forEach(function (field) {
                updateFieldState(field, conflicts[field.name] || '');
              });
            })
            .catch(function () {
              watchedFields.forEach(function (field) {
                updateFieldState(field, activeConflicts[field.name] || '');
              });
            });
        }

        function scheduleDuplicateCheck() {
          if (debounceTimer) {
            window.clearTimeout(debounceTimer);
          }

          debounceTimer = window.setTimeout(runDuplicateCheck, 350);
        }

        watchedFields.forEach(function (field) {
          ensureFeedbackElement(field);
          field.addEventListener('input', scheduleDuplicateCheck);
          field.addEventListener('blur', runDuplicateCheck);
        });

        runDuplicateCheck();
      });
    });
  </script>

  <script>


<script>
$(document).ready(function(){

$.ajaxSetup({
headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
}
});

$('body').on('click', '.delete-item', function(event){
event.preventDefault();

let deleteUrl = $(this).attr('href');

Swal.fire({
  title: "Tem certeza?",
  text: "Você não poderá reverter isso!",
  icon: "warning",
  showCancelButton: true,
  confirmButtonColor: "#1e5e2f",
  cancelButtonColor: "#d33",
  confirmButtonText: "Sim, exclua-o!"
}).then((result) => {
  if (result.isConfirmed) {

    $.ajax({
        type: 'DELETE',
        url: deleteUrl,

        success: function(data){

            if(data.status == 'success'){

                Swal.fire({
                title: "Excluído!",
                text: "Seu arquivo foi excluído com sucesso!",
                icon: "success"
                });

                window.location.reload();
            }

        },
        error: function(xhr, status, error){
          console.log(error);
        }

    })


  }
});

})


});
</script>
@stack('scripts')
</body>
</html>
