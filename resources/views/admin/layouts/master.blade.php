<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <meta name="csrf-token" content="{{ csrf_token()}}">
  <title>Painel Admisnistrativo </title>
  <script>
    (function () {
      try {
        var savedTheme = window.localStorage.getItem('admin.ui.theme') || 'light';
        document.documentElement.setAttribute('data-theme', savedTheme === 'dark' ? 'dark' : 'light');
      } catch (error) {
        document.documentElement.setAttribute('data-theme', 'light');
      }
    })();
  </script>

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
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.6/css/dataTables.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/2.0.6/css/dataTables.bootstrap5.css">


  <!-- CSS Toastr -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css">

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/assets/css/components.css') }}">
  <link rel="stylesheet" href="{{ asset('backend/assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}">

  <!-- Favicons -->
  <link rel="icon" type="image/svg+xml" href="{{ asset('backend/assets/img/cms-favicon.svg') }}">
  <link rel="shortcut icon" type="image/svg+xml" href="{{ asset('backend/assets/img/cms-favicon.svg') }}">
  <link rel="apple-touch-icon" href="{{ asset('backend/assets/img/cms-favicon.svg') }}">

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

@php
  $authenticatedUser = auth()->user();
  $isClinicManager = $authenticatedUser?->isClinicManager() ?? false;
  $isCadastrosBaseRoute = request()->routeIs('admin.settings.*');
  $successMessage = session('success');
@endphp

<body class="{{ $isClinicManager ? 'clinic-manager-user' : '' }} {{ $isClinicManager && ! $isCadastrosBaseRoute ? 'clinic-manager-readonly' : '' }}">
  <style>
    :root {
      --app-bg: linear-gradient(180deg, #f3f8fd 0%, #eef5fb 52%, #e7f0f9 100%);
      --main-content-offset: 72px;
      --surface-primary: #ffffff;
      --surface-secondary: #f7fbff;
      --surface-tertiary: #edf5fd;
      --border-soft: rgba(23, 111, 190, 0.14);
      --border-strong: rgba(23, 111, 190, 0.24);
      --text-primary: #16344d;
      --text-secondary: #567086;
      --text-muted: #7f96aa;
      --accent-primary: #176fbe;
      --accent-strong: #0f4f86;
      --shadow-soft: 0 16px 34px rgba(15, 23, 42, 0.08);
      --shadow-strong: 0 20px 44px rgba(15, 23, 42, 0.12);
      --input-bg: #ffffff;
      --input-border: rgba(23, 111, 190, 0.18);
      --row-alt: rgba(23, 111, 190, 0.025);
    }

    html[data-theme="dark"] {
      --app-bg: linear-gradient(180deg, #09111a 0%, #0e1722 52%, #101d2a 100%);
      --surface-primary: #132131;
      --surface-secondary: #16283b;
      --surface-tertiary: #1a3046;
      --border-soft: rgba(143, 197, 255, 0.14);
      --border-strong: rgba(143, 197, 255, 0.24);
      --text-primary: #eef5fc;
      --text-secondary: #bfd0e0;
      --text-muted: #92a9bf;
      --accent-primary: #76bbff;
      --accent-strong: #9ed0ff;
      --shadow-soft: 0 18px 40px rgba(2, 8, 15, 0.34);
      --shadow-strong: 0 24px 54px rgba(2, 8, 15, 0.42);
      --input-bg: #16283b;
      --input-border: rgba(143, 197, 255, 0.22);
      --row-alt: rgba(255, 255, 255, 0.02);
    }

    body {
      background: var(--app-bg);
      color: var(--text-primary);
    }

    .sidebar-toggle-fixed {
      position: fixed;
      top: 18px;
      left: 18px;
      z-index: 1200;
      min-height: 44px;
      min-width: 132px;
      padding: 10px 18px;
      border: 0;
      border-radius: 999px;
      background: linear-gradient(135deg, rgba(8, 37, 66, 0.96) 0%, rgba(17, 79, 138, 0.96) 100%);
      box-shadow: 0 14px 28px rgba(8, 37, 66, 0.22);
      color: #ffffff;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: .01em;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      line-height: 1;
      text-decoration: none !important;
      transition: background-color .2s ease, box-shadow .2s ease, transform .2s ease, left .2s ease;
    }

    @media (min-width: 1025px) {
      .sidebar-toggle-fixed {
        display: none;
      }
    }

    .sidebar-toggle-fixed:hover,
    .sidebar-toggle-fixed:focus {
      background: linear-gradient(135deg, rgba(10, 46, 82, 1) 0%, rgba(24, 102, 175, 1) 100%);
      color: #ffffff;
      text-decoration: none !important;
      transform: translateY(-1px);
      box-shadow: 0 16px 30px rgba(8, 37, 66, 0.26);
    }

    .sidebar-toggle-fixed[data-state="open"] {
      background: rgba(255, 255, 255, 0.95);
      color: #0f3d6b;
      box-shadow: 0 12px 26px rgba(8, 37, 66, 0.16);
    }

    .sidebar-toggle-fixed[data-state="open"]:hover,
    .sidebar-toggle-fixed[data-state="open"]:focus {
      background: #ffffff;
      color: #0d3358;
    }

    .section .section-body > .alert:first-child,
    .section .section-body > .account-shell > .alert:first-child {
      margin-top: 34px;
    }

    .layout-floating-alert {
      position: relative;
      z-index: 1105;
      box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
    }

    .layout-alert-section {
      position: relative;
      z-index: 1105;
      margin-top: 14px;
      padding-top: 6px;
    }

    .layout-alert-section .section-body {
      padding-top: 10px !important;
    }

    .main-content {
      min-width: 0;
      padding-top: var(--main-content-offset);
    }

    .main-content > .section,
    .main-content .section:first-child {
      margin-top: 0;
      padding-top: 0;
    }

    html[data-theme="dark"] body {
      background: var(--app-bg);
      color: var(--text-primary);
    }

    .main-content,
    .section,
    .section-body {
      background: transparent;
      color: var(--text-primary);
    }

    .card,
    .modal-content,
    .table,
    .dropdown-menu,
    .bg-white,
    .profile-dropdown,
    .dropdown-list,
    .list-group-item {
      background: var(--surface-primary) !important;
      color: var(--text-primary) !important;
      border-color: var(--border-soft) !important;
      box-shadow: var(--shadow-soft);
    }

    .card-header,
    .card-footer,
    .dropdown-header,
    .dropdown-footer,
    .profile-dropdown-header,
    .logout-form,
    .border-bottom,
    .modal-header,
    .modal-footer {
      background: var(--surface-secondary) !important;
      color: var(--text-primary) !important;
      border-color: var(--border-soft) !important;
    }

    .table td,
    .table th,
    .text-dark,
    .section-header h1,
    .card-header h4,
    .profile-name,
    .dropdown-item-desc strong,
    .modal-title,
    .section-header-breadcrumb,
    label,
    .form-check-label,
    .nav-tabs .nav-link {
      color: var(--text-primary) !important;
      border-color: var(--border-soft);
    }

    .text-muted,
    .profile-role,
    .dropdown-item-desc,
    .breadcrumb-item,
    .small,
    .section-header-breadcrumb .breadcrumb-item a,
    .table td .text-muted {
      color: var(--text-secondary) !important;
    }

    .table-striped tbody tr:nth-of-type(odd),
    .dropdown-item,
    .list-group-item {
      background-color: var(--row-alt) !important;
    }

    .table td,
    .table th,
    .table thead th,
    .table-bordered td,
    .table-bordered th,
    .dropdown-item,
    .list-group-item,
    .nav-tabs,
    .page-item .page-link {
      border-color: var(--border-soft) !important;
    }

    .form-control,
    .custom-select,
    textarea,
    select,
    input[type="text"],
    input[type="email"],
    input[type="number"],
    input[type="password"],
    input[type="date"],
    input[type="time"],
    input[type="color"] {
      background: var(--input-bg) !important;
      color: var(--text-primary) !important;
      border-color: var(--input-border) !important;
    }

    input[type="color"] {
      min-height: 44px;
      padding: 6px;
      border-radius: 14px;
    }

    .form-control[readonly],
    .form-control:disabled,
    .custom-select:disabled,
    select:disabled,
    input[readonly],
    textarea[readonly],
    .form-control.bg-light,
    .custom-select.bg-light,
    select.bg-light,
    input.bg-light,
    textarea.bg-light {
      background: var(--surface-secondary) !important;
      color: var(--text-primary) !important;
      border-color: var(--input-border) !important;
      box-shadow: none !important;
      opacity: 1;
    }

    .input-group-text,
    .input-group-append .input-group-text,
    .input-group-prepend .input-group-text {
      background: var(--surface-secondary) !important;
      color: var(--text-primary) !important;
      border-color: var(--input-border) !important;
    }

    .action-button-group {
      display: inline-flex;
      flex-wrap: nowrap;
      align-items: center;
      gap: 6px;
      white-space: nowrap;
    }

    .action-button-group form {
      margin: 0;
    }

    .action-button-group .btn,
    .action-button-group .text-muted {
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    @media (max-width: 767.98px) {
      .action-button-group {
        flex-wrap: wrap;
        justify-content: center;
      }

      .action-button-group > *,
      .action-button-group form,
      .action-button-group .btn {
        width: 100%;
      }

      .action-button-cell {
        min-width: 220px !important;
        white-space: normal !important;
      }
    }

    .selectric-wrapper {
      width: 100%;
    }

    .selectric-wrapper .selectric {
      background: var(--input-bg) !important;
      border-color: var(--input-border) !important;
      border-radius: 14px;
      color: var(--text-primary) !important;
    }

    .selectric-wrapper .selectric .label {
      color: var(--text-primary) !important;
      font-size: 0.95rem;
      line-height: 42px;
    }

    .selectric-wrapper .selectric .button {
      background: transparent !important;
      color: var(--text-secondary) !important;
    }

    .selectric-wrapper .selectric .button::after {
      border-top-color: var(--text-secondary) !important;
    }

    .selectric-open .selectric,
    .selectric-hover .selectric,
    .selectric-focus .selectric {
      border-color: var(--accent-primary) !important;
      box-shadow: 0 0 0 0.16rem color-mix(in srgb, var(--accent-primary) 22%, transparent) !important;
    }

    .selectric-items {
      background: var(--surface-primary) !important;
      border-color: var(--input-border) !important;
      box-shadow: var(--shadow-soft);
    }

    .selectric-items li {
      background: transparent !important;
      color: var(--text-primary) !important;
    }

    .selectric-items li.highlighted,
    .selectric-items li.selected,
    .selectric-items li:hover {
      background: var(--surface-secondary) !important;
      color: var(--text-primary) !important;
    }

    .form-control:focus,
    .custom-select:focus,
    textarea:focus,
    select:focus,
    input:focus {
      border-color: var(--accent-primary) !important;
      box-shadow: 0 0 0 0.16rem color-mix(in srgb, var(--accent-primary) 22%, transparent) !important;
    }

    .page-item .page-link,
    .btn-light,
    .btn-outline-secondary,
    .btn-outline-primary {
      background: var(--surface-primary);
      border-color: var(--border-strong);
      color: var(--text-primary);
    }

    .page-item .page-link {
      position: relative;
      min-width: 42px;
      min-height: 42px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.5rem 0.85rem;
      line-height: 1;
      border-radius: 12px !important;
      font-weight: 700;
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background-color .18s ease, color .18s ease;
      box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
    }

    .page-item .page-link:hover,
    .page-item .page-link:focus {
      transform: translateY(-2px) scale(1.03);
      border-color: var(--accent-primary) !important;
      box-shadow: 0 14px 26px rgba(23, 111, 190, 0.18);
      z-index: 2;
    }

    .page-item.active .page-link {
      background: linear-gradient(180deg, color-mix(in srgb, var(--accent-primary) 86%, white 14%) 0%, var(--accent-primary) 100%);
      border-color: var(--accent-primary) !important;
      color: #ffffff !important;
      box-shadow: 0 14px 26px rgba(23, 111, 190, 0.24);
    }

    .page-item.disabled .page-link {
      opacity: .55;
      box-shadow: none;
    }

    .btn {
      border-radius: 14px;
      font-weight: 700;
      letter-spacing: 0.01em;
      transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease, background-color .18s ease, color .18s ease;
    }

    .btn:hover,
    .btn:focus {
      transform: translateY(-1px);
    }

    .btn-secondary,
    .btn-outline-secondary,
    .btn-light {
      background: linear-gradient(180deg, var(--surface-secondary) 0%, var(--surface-tertiary) 100%);
      border-color: var(--border-strong);
      color: var(--text-primary);
      box-shadow: 0 10px 20px rgba(15, 23, 42, 0.06);
    }

    .btn-secondary {
      background: linear-gradient(180deg, #ff7a86 0%, #e45061 100%);
      border-color: #cf4253;
      color: #ffffff;
      box-shadow: 0 12px 24px rgba(207, 66, 83, 0.2);
    }

    .btn-secondary:hover,
    .btn-secondary:focus {
      background: linear-gradient(180deg, #ff6675 0%, #d93d50 100%);
      border-color: #bb3042;
      color: #ffffff;
      box-shadow: 0 14px 28px rgba(185, 48, 66, 0.24);
    }

    .btn-light {
      background: linear-gradient(180deg, #ffffff 0%, #eef6ff 100%);
      border: 1px solid rgba(23, 111, 190, 0.28);
      color: #17466d;
      box-shadow: 0 10px 20px rgba(23, 111, 190, 0.08);
    }

    .btn-form-action {
      min-width: 118px;
      min-height: 40px;
      padding: 0.52rem 1.05rem;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
    }

    .btn-form-compact {
      min-height: 40px;
      padding: 0.55rem 1rem;
      border-radius: 12px;
      font-weight: 700;
    }

    .btn-form-search {
      min-height: 32px;
      padding: 0.32rem 0.78rem;
      border-radius: 10px;
      font-size: 0.84rem;
      font-weight: 700;
    }

    .table .btn-sm,
    .table-responsive .btn-sm {
      min-height: 28px;
      padding: 0.24rem 0.58rem;
      border-radius: 9px;
      font-weight: 700;
      font-size: 0.76rem;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .btn-secondary:hover,
    .btn-secondary:focus,
    .btn-outline-secondary:hover,
    .btn-outline-secondary:focus,
    .btn-light:hover,
    .btn-light:focus {
      background: linear-gradient(180deg, color-mix(in srgb, var(--surface-secondary) 80%, var(--accent-primary) 20%) 0%, color-mix(in srgb, var(--surface-tertiary) 80%, var(--accent-primary) 20%) 100%);
      border-color: var(--accent-primary);
      color: var(--text-primary);
      box-shadow: 0 12px 24px rgba(15, 23, 42, 0.1);
    }

    .btn-light:hover,
    .btn-light:focus {
      background: linear-gradient(180deg, #f7fbff 0%, #dfeefe 100%);
      border-color: rgba(23, 111, 190, 0.42);
      color: #103a5a;
      box-shadow: 0 14px 26px rgba(23, 111, 190, 0.12);
    }

    html[data-theme="dark"] .btn-secondary,
    html[data-theme="dark"] .btn-outline-secondary,
    html[data-theme="dark"] .btn-light {
      background: linear-gradient(180deg, rgba(23, 40, 59, 0.98) 0%, rgba(19, 33, 49, 0.98) 100%);
      border-color: rgba(143, 197, 255, 0.24);
      color: #eef5fc;
      box-shadow: none;
    }

    html[data-theme="dark"] .btn-secondary {
      background: linear-gradient(180deg, #d85b6a 0%, #a92d3d 100%) !important;
      border-color: rgba(255, 153, 166, 0.32) !important;
      color: #fff3f5 !important;
      box-shadow: 0 12px 24px rgba(116, 20, 34, 0.28) !important;
    }

    html[data-theme="dark"] .btn-secondary:hover,
    html[data-theme="dark"] .btn-secondary:focus {
      background: linear-gradient(180deg, #ea6b7b 0%, #b53244 100%) !important;
      border-color: rgba(255, 181, 191, 0.4) !important;
      color: #ffffff !important;
    }

    html[data-theme="dark"] .btn-light {
      background: linear-gradient(180deg, rgba(26, 49, 73, 0.98) 0%, rgba(20, 39, 58, 0.98) 100%) !important;
      border-color: rgba(118, 187, 255, 0.34) !important;
      color: #dff0ff !important;
      box-shadow: 0 12px 22px rgba(6, 26, 46, 0.18) !important;
    }

    html[data-theme="dark"] .btn-secondary:hover,
    html[data-theme="dark"] .btn-secondary:focus,
    html[data-theme="dark"] .btn-outline-secondary:hover,
    html[data-theme="dark"] .btn-outline-secondary:focus,
    html[data-theme="dark"] .btn-light:hover,
    html[data-theme="dark"] .btn-light:focus {
      background: linear-gradient(180deg, rgba(35, 62, 88, 0.98) 0%, rgba(26, 47, 69, 0.98) 100%);
      border-color: rgba(158, 208, 255, 0.34);
      color: #ffffff;
    }

    html[data-theme="dark"] .btn-light:hover,
    html[data-theme="dark"] .btn-light:focus {
      background: linear-gradient(180deg, rgba(34, 63, 92, 0.98) 0%, rgba(24, 47, 70, 0.98) 100%) !important;
      border-color: rgba(158, 208, 255, 0.44) !important;
      color: #ffffff !important;
    }

    .action-button-group .btn-secondary,
    .settings-actions .btn-secondary,
    .users-actions .btn-secondary,
    .professional-actions .btn-secondary,
    .patients-actions .btn-secondary,
    .patient-history-actions .btn-secondary,
    .queue-actions .btn-secondary,
    .agenda-actions .btn-secondary,
    .confirmation-actions .btn-secondary,
    .waitlist-actions .btn-secondary {
      background: linear-gradient(180deg, #2f8fb2 0%, #1d6f93 100%) !important;
      border-color: #195f7d !important;
      color: #ffffff !important;
      box-shadow: 0 12px 24px rgba(24, 92, 121, 0.28) !important;
    }

    .action-button-group .btn-secondary:hover,
    .action-button-group .btn-secondary:focus,
    .settings-actions .btn-secondary:hover,
    .settings-actions .btn-secondary:focus,
    .users-actions .btn-secondary:hover,
    .users-actions .btn-secondary:focus,
    .professional-actions .btn-secondary:hover,
    .professional-actions .btn-secondary:focus,
    .patients-actions .btn-secondary:hover,
    .patients-actions .btn-secondary:focus,
    .patient-history-actions .btn-secondary:hover,
    .patient-history-actions .btn-secondary:focus,
    .queue-actions .btn-secondary:hover,
    .queue-actions .btn-secondary:focus,
    .agenda-actions .btn-secondary:hover,
    .agenda-actions .btn-secondary:focus,
    .confirmation-actions .btn-secondary:hover,
    .confirmation-actions .btn-secondary:focus,
    .waitlist-actions .btn-secondary:hover,
    .waitlist-actions .btn-secondary:focus {
      background: linear-gradient(180deg, #39a1c7 0%, #247d9f 100%) !important;
      border-color: #1c6d8d !important;
      color: #ffffff !important;
      box-shadow: 0 14px 28px rgba(24, 92, 121, 0.34) !important;
    }

    .action-button-group .btn-info,
    .settings-actions .btn-info,
    .users-actions .btn-info,
    .professional-actions .btn-info,
    .patients-actions .btn-info,
    .patient-history-actions .btn-info,
    .queue-actions .btn-info,
    .agenda-actions .btn-info,
    .confirmation-actions .btn-info,
    .waitlist-actions .btn-info {
      background: linear-gradient(180deg, #2fb8d0 0%, #1389ab 100%) !important;
      border-color: #0f7794 !important;
      color: #ffffff !important;
      box-shadow: 0 12px 24px rgba(15, 119, 148, 0.22) !important;
    }

    .action-button-group .btn-info:hover,
    .action-button-group .btn-info:focus,
    .settings-actions .btn-info:hover,
    .settings-actions .btn-info:focus,
    .users-actions .btn-info:hover,
    .users-actions .btn-info:focus,
    .professional-actions .btn-info:hover,
    .professional-actions .btn-info:focus,
    .patients-actions .btn-info:hover,
    .patients-actions .btn-info:focus,
    .patient-history-actions .btn-info:hover,
    .patient-history-actions .btn-info:focus,
    .queue-actions .btn-info:hover,
    .queue-actions .btn-info:focus,
    .agenda-actions .btn-info:hover,
    .agenda-actions .btn-info:focus,
    .confirmation-actions .btn-info:hover,
    .confirmation-actions .btn-info:focus,
    .waitlist-actions .btn-info:hover,
    .waitlist-actions .btn-info:focus {
      background: linear-gradient(180deg, #3fc4db 0%, #1694b8 100%) !important;
      border-color: #1180a0 !important;
      color: #ffffff !important;
      box-shadow: 0 14px 28px rgba(15, 119, 148, 0.28) !important;
    }

    .action-button-group .btn-success,
    .settings-actions .btn-success,
    .users-actions .btn-success,
    .professional-actions .btn-success,
    .patients-actions .btn-success,
    .patient-history-actions .btn-success,
    .queue-actions .btn-success,
    .agenda-actions .btn-success,
    .confirmation-actions .btn-success,
    .waitlist-actions .btn-success {
      background: linear-gradient(180deg, #34be74 0%, #218b52 100%) !important;
      border-color: #1c7846 !important;
      color: #ffffff !important;
      box-shadow: 0 12px 24px rgba(28, 120, 70, 0.24) !important;
    }

    .action-button-group .btn-success:hover,
    .action-button-group .btn-success:focus,
    .settings-actions .btn-success:hover,
    .settings-actions .btn-success:focus,
    .users-actions .btn-success:hover,
    .users-actions .btn-success:focus,
    .professional-actions .btn-success:hover,
    .professional-actions .btn-success:focus,
    .patients-actions .btn-success:hover,
    .patients-actions .btn-success:focus,
    .patient-history-actions .btn-success:hover,
    .patient-history-actions .btn-success:focus,
    .queue-actions .btn-success:hover,
    .queue-actions .btn-success:focus,
    .agenda-actions .btn-success:hover,
    .agenda-actions .btn-success:focus,
    .confirmation-actions .btn-success:hover,
    .confirmation-actions .btn-success:focus,
    .waitlist-actions .btn-success:hover,
    .waitlist-actions .btn-success:focus {
      background: linear-gradient(180deg, #40ca7e 0%, #25965a 100%) !important;
      border-color: #20824d !important;
      color: #ffffff !important;
      box-shadow: 0 14px 28px rgba(28, 120, 70, 0.3) !important;
    }

    .action-button-group .btn-warning,
    .settings-actions .btn-warning,
    .users-actions .btn-warning,
    .professional-actions .btn-warning,
    .patients-actions .btn-warning,
    .patient-history-actions .btn-warning,
    .queue-actions .btn-warning,
    .agenda-actions .btn-warning,
    .confirmation-actions .btn-warning,
    .waitlist-actions .btn-warning {
      background: linear-gradient(180deg, #ffc44d 0%, #e89b12 100%) !important;
      border-color: #cf8500 !important;
      color: #4a2b00 !important;
      box-shadow: 0 12px 24px rgba(207, 133, 0, 0.22) !important;
    }

    .action-button-group .btn-warning:hover,
    .action-button-group .btn-warning:focus,
    .settings-actions .btn-warning:hover,
    .settings-actions .btn-warning:focus,
    .users-actions .btn-warning:hover,
    .users-actions .btn-warning:focus,
    .professional-actions .btn-warning:hover,
    .professional-actions .btn-warning:focus,
    .patients-actions .btn-warning:hover,
    .patients-actions .btn-warning:focus,
    .patient-history-actions .btn-warning:hover,
    .patient-history-actions .btn-warning:focus,
    .queue-actions .btn-warning:hover,
    .queue-actions .btn-warning:focus,
    .agenda-actions .btn-warning:hover,
    .agenda-actions .btn-warning:focus,
    .confirmation-actions .btn-warning:hover,
    .confirmation-actions .btn-warning:focus,
    .waitlist-actions .btn-warning:hover,
    .waitlist-actions .btn-warning:focus {
      background: linear-gradient(180deg, #ffd061 0%, #f0a317 100%) !important;
      border-color: #da8b00 !important;
      color: #432700 !important;
      box-shadow: 0 14px 28px rgba(207, 133, 0, 0.28) !important;
    }

    .action-button-group .btn-danger,
    .settings-actions .btn-danger,
    .users-actions .btn-danger,
    .professional-actions .btn-danger,
    .patients-actions .btn-danger,
    .patient-history-actions .btn-danger,
    .queue-actions .btn-danger,
    .agenda-actions .btn-danger,
    .confirmation-actions .btn-danger,
    .waitlist-actions .btn-danger {
      background: linear-gradient(180deg, #ef6673 0%, #c73b4c 100%) !important;
      border-color: #b23242 !important;
      color: #ffffff !important;
      box-shadow: 0 12px 24px rgba(178, 50, 66, 0.24) !important;
    }

    .action-button-group .btn-danger:hover,
    .action-button-group .btn-danger:focus,
    .settings-actions .btn-danger:hover,
    .settings-actions .btn-danger:focus,
    .users-actions .btn-danger:hover,
    .users-actions .btn-danger:focus,
    .professional-actions .btn-danger:hover,
    .professional-actions .btn-danger:focus,
    .patients-actions .btn-danger:hover,
    .patients-actions .btn-danger:focus,
    .patient-history-actions .btn-danger:hover,
    .patient-history-actions .btn-danger:focus,
    .queue-actions .btn-danger:hover,
    .queue-actions .btn-danger:focus,
    .agenda-actions .btn-danger:hover,
    .agenda-actions .btn-danger:focus,
    .confirmation-actions .btn-danger:hover,
    .confirmation-actions .btn-danger:focus,
    .waitlist-actions .btn-danger:hover,
    .waitlist-actions .btn-danger:focus {
      background: linear-gradient(180deg, #f47782 0%, #cf4354 100%) !important;
      border-color: #bc3748 !important;
      color: #ffffff !important;
      box-shadow: 0 14px 28px rgba(178, 50, 66, 0.3) !important;
    }

    html[data-theme="dark"] .action-button-group .btn-secondary,
    html[data-theme="dark"] .settings-actions .btn-secondary,
    html[data-theme="dark"] .users-actions .btn-secondary,
    html[data-theme="dark"] .professional-actions .btn-secondary,
    html[data-theme="dark"] .patients-actions .btn-secondary,
    html[data-theme="dark"] .patient-history-actions .btn-secondary,
    html[data-theme="dark"] .queue-actions .btn-secondary,
    html[data-theme="dark"] .agenda-actions .btn-secondary,
    html[data-theme="dark"] .confirmation-actions .btn-secondary,
    html[data-theme="dark"] .waitlist-actions .btn-secondary,
    html[data-theme="dark"] .action-button-group .btn-info,
    html[data-theme="dark"] .settings-actions .btn-info,
    html[data-theme="dark"] .users-actions .btn-info,
    html[data-theme="dark"] .professional-actions .btn-info,
    html[data-theme="dark"] .patients-actions .btn-info,
    html[data-theme="dark"] .patient-history-actions .btn-info,
    html[data-theme="dark"] .queue-actions .btn-info,
    html[data-theme="dark"] .agenda-actions .btn-info,
    html[data-theme="dark"] .confirmation-actions .btn-info,
    html[data-theme="dark"] .waitlist-actions .btn-info,
    html[data-theme="dark"] .action-button-group .btn-success,
    html[data-theme="dark"] .settings-actions .btn-success,
    html[data-theme="dark"] .users-actions .btn-success,
    html[data-theme="dark"] .professional-actions .btn-success,
    html[data-theme="dark"] .patients-actions .btn-success,
    html[data-theme="dark"] .patient-history-actions .btn-success,
    html[data-theme="dark"] .queue-actions .btn-success,
    html[data-theme="dark"] .agenda-actions .btn-success,
    html[data-theme="dark"] .confirmation-actions .btn-success,
    html[data-theme="dark"] .waitlist-actions .btn-success,
    html[data-theme="dark"] .action-button-group .btn-warning,
    html[data-theme="dark"] .settings-actions .btn-warning,
    html[data-theme="dark"] .users-actions .btn-warning,
    html[data-theme="dark"] .professional-actions .btn-warning,
    html[data-theme="dark"] .patients-actions .btn-warning,
    html[data-theme="dark"] .patient-history-actions .btn-warning,
    html[data-theme="dark"] .queue-actions .btn-warning,
    html[data-theme="dark"] .agenda-actions .btn-warning,
    html[data-theme="dark"] .confirmation-actions .btn-warning,
    html[data-theme="dark"] .waitlist-actions .btn-warning,
    html[data-theme="dark"] .action-button-group .btn-danger,
    html[data-theme="dark"] .settings-actions .btn-danger,
    html[data-theme="dark"] .users-actions .btn-danger,
    html[data-theme="dark"] .professional-actions .btn-danger,
    html[data-theme="dark"] .patients-actions .btn-danger,
    html[data-theme="dark"] .patient-history-actions .btn-danger,
    html[data-theme="dark"] .queue-actions .btn-danger,
    html[data-theme="dark"] .agenda-actions .btn-danger,
    html[data-theme="dark"] .confirmation-actions .btn-danger,
    html[data-theme="dark"] .waitlist-actions .btn-danger {
      box-shadow: 0 12px 26px rgba(5, 12, 24, 0.28) !important;
    }

    html[data-theme="dark"] .action-button-group .btn-secondary,
    html[data-theme="dark"] .settings-actions .btn-secondary,
    html[data-theme="dark"] .users-actions .btn-secondary,
    html[data-theme="dark"] .professional-actions .btn-secondary,
    html[data-theme="dark"] .patients-actions .btn-secondary,
    html[data-theme="dark"] .patient-history-actions .btn-secondary,
    html[data-theme="dark"] .queue-actions .btn-secondary,
    html[data-theme="dark"] .agenda-actions .btn-secondary,
    html[data-theme="dark"] .confirmation-actions .btn-secondary,
    html[data-theme="dark"] .waitlist-actions .btn-secondary {
      background: linear-gradient(180deg, #2d86aa 0%, #1c6282 100%) !important;
      border-color: #195672 !important;
      box-shadow: 0 16px 30px rgba(8, 34, 48, 0.4) !important;
    }

    html[data-theme="dark"] .action-button-group .btn-secondary:hover,
    html[data-theme="dark"] .action-button-group .btn-secondary:focus,
    html[data-theme="dark"] .settings-actions .btn-secondary:hover,
    html[data-theme="dark"] .settings-actions .btn-secondary:focus,
    html[data-theme="dark"] .users-actions .btn-secondary:hover,
    html[data-theme="dark"] .users-actions .btn-secondary:focus,
    html[data-theme="dark"] .professional-actions .btn-secondary:hover,
    html[data-theme="dark"] .professional-actions .btn-secondary:focus,
    html[data-theme="dark"] .patients-actions .btn-secondary:hover,
    html[data-theme="dark"] .patients-actions .btn-secondary:focus,
    html[data-theme="dark"] .patient-history-actions .btn-secondary:hover,
    html[data-theme="dark"] .patient-history-actions .btn-secondary:focus,
    html[data-theme="dark"] .queue-actions .btn-secondary:hover,
    html[data-theme="dark"] .queue-actions .btn-secondary:focus,
    html[data-theme="dark"] .agenda-actions .btn-secondary:hover,
    html[data-theme="dark"] .agenda-actions .btn-secondary:focus,
    html[data-theme="dark"] .confirmation-actions .btn-secondary:hover,
    html[data-theme="dark"] .confirmation-actions .btn-secondary:focus,
    html[data-theme="dark"] .waitlist-actions .btn-secondary:hover,
    html[data-theme="dark"] .waitlist-actions .btn-secondary:focus {
      background: linear-gradient(180deg, #38a0c5 0%, #25799b 100%) !important;
      border-color: #1d6887 !important;
      box-shadow: 0 18px 34px rgba(8, 34, 48, 0.44) !important;
    }

    .section-header {
      background: var(--surface-primary) !important;
      border: 1px solid var(--border-soft) !important;
      border-radius: 20px;
      box-shadow: var(--shadow-soft);
      padding: 10px 24px;
      margin-top: 0;
      margin-bottom: 14px;
      min-height: auto;
    }

    .section-header-breadcrumb {
      display: none !important;
    }

    .card,
    .modal-content,
    .section-header {
      border-radius: 20px;
      overflow: hidden;
    }

    .main-footer {
      background: var(--surface-primary);
      color: var(--text-secondary);
      border-top: 1px solid var(--border-soft);
    }

    .badge-light,
    .bg-light {
      background: var(--surface-tertiary) !important;
      color: var(--text-primary) !important;
      border-color: var(--border-soft) !important;
    }

    html[data-theme="dark"] .alert-warning.layout-floating-alert {
      background: #3d3113;
      border-color: #8f6d19;
      color: #ffe7a0;
    }

    html[data-theme="dark"] .alert-danger.layout-floating-alert {
      background: #3b1920;
      border-color: #8f3143;
      color: #ffc0ca;
    }

    html[data-theme="dark"] .btn-primary,
    html[data-theme="dark"] .btn-info,
    html[data-theme="dark"] .btn-success,
    html[data-theme="dark"] .btn-warning,
    html[data-theme="dark"] .btn-danger {
      box-shadow: none;
    }

    body.sidebar-gone .navbar {
      left: 0;
    }

    body.sidebar-gone .main-content {
      padding-left: 30px;
    }

    body.sidebar-gone .main-footer {
      padding-left: 30px;
    }

    .section-header,
    .card-header,
    .card-header-action,
    .section-header-breadcrumb {
      flex-wrap: wrap;
      gap: 12px;
    }

    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }

    .table-responsive .btn,
    .table-responsive form {
      white-space: nowrap;
    }

    .card-header form[class*="d-flex"] {
      width: auto;
      max-width: 100%;
    }

    .card-header form[class*="d-flex"] .form-group {
      min-width: 0;
    }

    .card-header form[class*="d-flex"] .form-control,
    .card-header form[class*="d-flex"] .custom-select,
    .card-header form[class*="d-flex"] select,
    .card-header form[class*="d-flex"] input[style*="min-width"],
    .card-header form[class*="d-flex"] select[style*="min-width"] {
      max-width: 100%;
    }

    @media (max-width: 767.98px) {
      :root {
        --main-content-offset: 76px;
      }

      body {
        overflow-x: hidden;
      }

      .main-content {
        padding-left: 12px;
        padding-right: 12px;
      }

      .section,
      .section-body {
        overflow-x: clip;
      }

      .section-header {
        align-items: flex-start;
        padding: 16px;
      }

      .section-header h1,
      .card-header h4 {
        width: 100%;
      }

      .section-header-breadcrumb {
        width: 100%;
      }

      .card-header-action,
      .card-header-action .btn,
      .section-header .btn,
      .section-body .btn-block-mobile {
        width: 100%;
      }

      .card,
      .section-header,
      .modal-content {
        border-radius: 16px;
      }

      .card-header {
        align-items: flex-start !important;
      }

      .card-body,
      .card-header,
      .card-footer,
      .modal-body,
      .modal-header,
      .modal-footer {
        padding-left: 16px !important;
        padding-right: 16px !important;
      }

      .row {
        margin-left: -8px;
        margin-right: -8px;
      }

      .row > [class*="col-"] {
        padding-left: 8px;
        padding-right: 8px;
      }

      .form-control,
      .custom-select,
      select,
      input,
      textarea,
      .btn {
        min-height: 44px;
        max-width: 100%;
      }

      .btn-form-action,
      .btn-form-compact,
      .btn-form-search {
        width: 100%;
      }

      .table-responsive {
        margin-left: -4px;
        margin-right: -4px;
        padding-bottom: 4px;
      }

      .table-responsive table,
      .table-responsive .table {
        min-width: 0 !important;
      }

      .table-responsive .table td,
      .table-responsive .table th {
        white-space: normal;
        word-break: break-word;
      }

      .table-responsive .btn,
      .table-responsive .btn-sm,
      .table-responsive .badge,
      .table-responsive form,
      .table-responsive .d-inline-flex,
      .table-responsive .d-inline-block {
        white-space: nowrap;
      }

      .modal-dialog {
        margin: 10px;
      }
    }

    body:not(.sidebar-gone) .sidebar-toggle-fixed {
      left: 266px;
    }

    body.sidebar-mini .sidebar-toggle-fixed {
      left: 82px;
    }

    @media (max-width: 1024px) {
      .sidebar-toggle-fixed,
      body:not(.sidebar-gone) .sidebar-toggle-fixed,
      body.sidebar-mini .sidebar-toggle-fixed {
        left: 14px;
        top: 14px;
      }

      .layout-alert-section {
        margin-top: 0;
      }

      .main-content {
        padding-left: 18px;
        padding-right: 18px;
      }

      .section-header,
      .card-header {
        align-items: flex-start;
      }

      .section-header h1,
      .card-header h4 {
        width: 100%;
      }

      .card-header-action {
        width: 100%;
      }

      .card-header-action > * {
        flex: 1 1 100%;
      }

      .card-header-action .btn,
      .card-header-action .form-control,
      .card-header-action .form-control-sm,
      .card-header-action select {
        width: 100%;
      }

      .card-header form[class*="d-flex"],
      .card-header .d-flex.flex-wrap,
      .section-body form .d-flex.flex-wrap,
      .section-body .d-flex.flex-wrap[style*="gap"] {
        width: 100%;
      }

      .card-header form[class*="d-flex"] > *,
      .card-header .d-flex.flex-wrap > *,
      .section-body form .d-flex.flex-wrap > *,
      .section-body .d-flex.flex-wrap[style*="gap"] > * {
        flex: 1 1 100%;
        width: 100%;
        min-width: 0 !important;
      }

      .card-header form[class*="d-flex"] .form-group,
      .card-header form[class*="d-flex"] .form-control,
      .card-header form[class*="d-flex"] .custom-select,
      .card-header form[class*="d-flex"] select,
      .card-header form[class*="d-flex"] input,
      .card-header form[class*="d-flex"] button,
      .card-header form[class*="d-flex"] a,
      .card-header form[class*="d-flex"] input[style*="min-width"],
      .card-header form[class*="d-flex"] select[style*="min-width"] {
        width: 100% !important;
        min-width: 0 !important;
      }

      .card-statistic-1 .card-icon,
      .card-statistic-2 .card-icon {
        margin: 16px 16px 0;
      }

      .card.card-statistic-1 .card-wrap,
      .card.card-statistic-2 .card-wrap {
        min-height: 0;
        padding: 16px;
      }

      .main-footer .footer-left,
      .main-footer .footer-right {
        width: 100%;
        text-align: center;
      }
    }
  </style>
  <script>
    (function () {
      try {
        var savedSidebarState = window.localStorage.getItem('admin.ui.sidebar-state');

        if (!document.body) {
          return;
        }

        if (window.innerWidth > 1024) {
          if (savedSidebarState === 'mini') {
            document.body.classList.add('sidebar-mini');
          } else {
            document.body.classList.remove('sidebar-mini');
          }

          document.body.classList.remove('sidebar-gone');
          document.body.classList.remove('sidebar-show');
          return;
        }

        if (savedSidebarState === 'closed') {
          document.body.classList.add('sidebar-gone');
          document.body.classList.remove('sidebar-show');
        }
      } catch (error) {
      }
    })();
  </script>
  <div id="app">
    <div class="main-wrapper main-wrapper-1">
      <div class=""></div>
      <a href="#" id="sidebar-toggle-fixed" data-toggle="sidebar" class="sidebar-toggle-fixed" data-state="closed" aria-label="Abrir lateral" aria-pressed="false">Abrir lateral</a>

      <!-- START NAVABAR - MAYKONSILVEIRA.COM.BR -->
      @include('admin.layouts.navbar')

      <!-- END NAVBAR - MAYKONSILVEIRA.COM.BR -->

      <!-- START SIDEBAR - MAYKONSILVEIRA.COM.BR -->
      @include('admin.layouts.sidebar')

      <!-- END SIDEBAR - MAYKONSILVEIRA.COM.BR -->

      <!-- START MAIN CONTENT - MAYKONSILVEIRA.COM.BR -->
      <div class="main-content">

        @if(session('layout_warning') || session('layout_error'))
          <div class="section layout-alert-section">
            <div class="section-body pt-3">
              @if(session('layout_warning'))
                <div class="alert alert-warning mb-4 layout-floating-alert">{{ session('layout_warning') }}</div>
              @endif
              @if(session('layout_error'))
                <div class="alert alert-danger mb-4 layout-floating-alert">{{ session('layout_error') }}</div>
              @endif
            </div>
          </div>
        @endif

        @if($successMessage && str_contains($successMessage, 'O registro já está em Agendamentos Finalizados.'))
          <div class="section">
            <div class="section-body">
              <div class="alert alert-success mt-3 mb-4">{{ $successMessage }}</div>
            </div>
          </div>
        @endif

        @yield('content')

      </div>
      <!-- END MAIN CONTENT - MAYKONSILVEIRA.COM.BR -->

      <!-- START FOOTER - MAYKONSILVEIRA.COM.BR -->
      <footer class="main-footer">
        <div class="footer-left">
          Todos os Direitos Reservados <div class="bullet"></div> Desenvolvido Thiago Cruz Ferreira De Melo Versão 1.0
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
  <script src="{{ asset('backend/assets/modules/upload-preview/assets/js/jquery.uploadPreview.min.js') }}"></script>
  <script src="{{ asset('backend/assets/modules/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}"></script>
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


  @if(request()->routeIs('admin.dashboard'))
    <!-- Page Specific JS File -->
    <script src="{{ asset('backend/assets/js/page/index-0.js') }}"></script>
  @endif

  <!-- Template JS File -->
  <script src="{{ asset('backend/assets/js/scripts.js') }}"></script>
  <script src="{{ asset('backend/assets/js/custom.js') }}"></script>
  <script src="{{ asset('backend/assets/js/jmask.js') }}"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var htmlElement = document.documentElement;
      var themeToggleButtons = Array.from(document.querySelectorAll('[data-theme-toggle]'));

      function applyTheme(theme) {
        var normalizedTheme = theme === 'dark' ? 'dark' : 'light';

        htmlElement.setAttribute('data-theme', normalizedTheme);

        themeToggleButtons.forEach(function (button) {
          var icon = button.querySelector('[data-theme-icon]');
          var label = button.querySelector('[data-theme-label]');
          var thumb = button.querySelector('[data-theme-thumb]');
          var isDark = normalizedTheme === 'dark';
          var nextThemeIsDark = !isDark;

          button.setAttribute('aria-pressed', isDark ? 'true' : 'false');
          button.classList.toggle('is-dark', isDark);
          button.setAttribute('title', nextThemeIsDark ? 'Ativar modo escuro' : 'Ativar modo claro');

          if (icon) {
            icon.className = isDark ? 'fas fa-moon' : 'fas fa-sun';
          }

          if (thumb) {
            thumb.setAttribute('aria-hidden', 'true');
          }

          if (label) {
            label.textContent = isDark ? 'Modo Escuro' : 'Modo Claro';
          }
        });
      }

      if (themeToggleButtons.length) {
        applyTheme(htmlElement.getAttribute('data-theme') || 'light');

        themeToggleButtons.forEach(function (button) {
          button.addEventListener('click', function (event) {
            event.preventDefault();

            var nextTheme = (htmlElement.getAttribute('data-theme') || 'light') === 'dark' ? 'light' : 'dark';

            applyTheme(nextTheme);

            try {
              window.localStorage.setItem('admin.ui.theme', nextTheme);
            } catch (error) {
              // Ignora falhas de persistencia sem interromper a interface.
            }
          });
        });
      }

      if (window.jQuery && jQuery.fn && typeof jQuery.fn.selectric === 'function') {
        jQuery('select[data-enhanced-select="true"]').each(function () {
          var selectField = jQuery(this);

          if (!selectField.data('selectric')) {
            selectField.selectric();
          }
        });
      }

      if (
        window.jQuery
        && typeof jQuery.uploadPreview === 'function'
        && document.getElementById('image-upload')
        && document.getElementById('image-preview')
        && document.getElementById('image-label')
      ) {
        jQuery.uploadPreview({
          input_field: '#image-upload',
          preview_box: '#image-preview',
          label_field: '#image-label',
          label_default: 'Escolher arquivo',
          label_selected: 'Alterar arquivo',
          no_label: false,
          success_callback: null,
        });
      }

      if (window.jQuery && jQuery.fn && typeof jQuery.fn.tagsinput === 'function' && document.querySelector('.inputtags')) {
        jQuery('.inputtags').tagsinput('items');
      }
    });
  </script>

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
      var sidebarStateStorageKey = 'admin.ui.sidebar-state';
      var sidebarMenusStorageKey = 'admin.ui.sidebar-open-menus';
      var sidebarToggleButton = document.getElementById('sidebar-toggle-fixed');
      var sidebarDropdownLinks = Array.from(document.querySelectorAll('.main-sidebar .nav-link.has-dropdown'));

      function getSidebarMenuKey(link) {
        var label = link ? link.textContent : '';
        return (label || '').replace(/\s+/g, ' ').trim();
      }

      function applySidebarStateFromStorage() {
        if (!window.localStorage) {
          return;
        }

        var savedState = window.localStorage.getItem(sidebarStateStorageKey);

        if (window.innerWidth > 1024) {
          document.body.classList.remove('sidebar-gone');
          document.body.classList.remove('sidebar-show');

          if (savedState === 'mini') {
            document.body.classList.add('sidebar-mini');
          } else {
            document.body.classList.remove('sidebar-mini');
          }

          return;
        }

        if (savedState === 'open') {
          document.body.classList.remove('sidebar-gone');
          document.body.classList.remove('sidebar-mini');
        }

        if (savedState === 'closed') {
          document.body.classList.add('sidebar-gone');
          document.body.classList.remove('sidebar-show');
        }
      }

      function restoreOpenSidebarMenus() {
        if (!window.localStorage || window.innerWidth > 1024) {
          return;
        }

        var storedMenus = [];

        try {
          storedMenus = JSON.parse(window.localStorage.getItem(sidebarMenusStorageKey) || '[]');
        } catch (error) {
          storedMenus = [];
        }

        sidebarDropdownLinks.forEach(function (link) {
          var dropdown = link.closest('.dropdown');
          var menu = dropdown ? dropdown.querySelector('.dropdown-menu') : null;
          var shouldBeOpen = storedMenus.indexOf(getSidebarMenuKey(link)) !== -1;

          if (!dropdown || !menu) {
            return;
          }

          if (shouldBeOpen) {
            dropdown.classList.add('active');
            link.setAttribute('aria-expanded', 'true');
            menu.style.display = 'block';
            menu.classList.add('persisted-open');
          } else {
            dropdown.classList.remove('active');
            link.setAttribute('aria-expanded', 'false');
            menu.style.display = '';
            menu.classList.remove('persisted-open');
          }
        });
      }

      function persistSidebarPreferences() {
        if (!window.localStorage) {
          return;
        }

        var savedState = 'open';

        if (window.innerWidth > 1024) {
          savedState = document.body.classList.contains('sidebar-mini') ? 'mini' : 'open';
        } else if (document.body.classList.contains('sidebar-gone') && !document.body.classList.contains('sidebar-show')) {
          savedState = 'closed';
        }

        var openMenus = sidebarDropdownLinks
          .filter(function (link) {
            var dropdown = link.closest('.dropdown');
            return dropdown && dropdown.classList.contains('active');
          })
          .map(getSidebarMenuKey);

        window.localStorage.setItem(sidebarStateStorageKey, savedState);
        window.localStorage.setItem(sidebarMenusStorageKey, JSON.stringify(openMenus));
      }

      applySidebarStateFromStorage();
      restoreOpenSidebarMenus();
      window.setTimeout(applySidebarStateFromStorage, 60);
      window.setTimeout(restoreOpenSidebarMenus, 180);
      window.setTimeout(applySidebarStateFromStorage, 320);
      window.setTimeout(restoreOpenSidebarMenus, 650);

      if (sidebarToggleButton) {
        var updateSidebarToggleLabel = function () {
          if (window.innerWidth > 1024) {
            return;
          }

          var isClosed = document.body.classList.contains('sidebar-gone') && !document.body.classList.contains('sidebar-show');

          sidebarToggleButton.textContent = isClosed ? 'Abrir lateral' : 'Fechar lateral';
          sidebarToggleButton.setAttribute('data-state', isClosed ? 'closed' : 'open');
          sidebarToggleButton.setAttribute('aria-label', isClosed ? 'Abrir lateral' : 'Fechar lateral');
          sidebarToggleButton.setAttribute('aria-pressed', isClosed ? 'false' : 'true');
        };

        updateSidebarToggleLabel();

        sidebarToggleButton.addEventListener('click', function () {
          window.setTimeout(function () {
            updateSidebarToggleLabel();
            persistSidebarPreferences();
          }, 20);
        });

        var sidebarObserver = new MutationObserver(function () {
          updateSidebarToggleLabel();
          persistSidebarPreferences();
        });
        sidebarObserver.observe(document.body, { attributes: true, attributeFilter: ['class'] });
      }

      sidebarDropdownLinks.forEach(function (link) {
        link.addEventListener('click', function () {
          window.setTimeout(function () {
            persistSidebarPreferences();
          }, 520);
        });
      });

      window.addEventListener('resize', function () {
        applySidebarStateFromStorage();
        restoreOpenSidebarMenus();
      });

      document.querySelectorAll('.main-sidebar .dropdown-menu a').forEach(function (link) {
        link.addEventListener('click', function () {
          persistSidebarPreferences();
        });
      });

      window.addEventListener('load', function () {
        applySidebarStateFromStorage();
        restoreOpenSidebarMenus();
        window.setTimeout(applySidebarStateFromStorage, 120);
        persistSidebarPreferences();
      });

      if (document.body.classList.contains('clinic-manager-readonly')) {
        var mainContent = document.querySelector('.main-content');

        if (mainContent) {
          mainContent.querySelectorAll('form').forEach(function (form) {
            var spoofedMethodInput = form.querySelector('input[name="_method"]');
            var method = ((spoofedMethodInput ? spoofedMethodInput.value : form.getAttribute('method')) || 'GET').toUpperCase();

            if (method !== 'GET') {
              form.style.display = 'none';
            }
          });

          mainContent.querySelectorAll('a[href*="/create"], a[href*="/edit"], [data-target*="edit-"], [data-target*="create-"]').forEach(function (element) {
            element.style.display = 'none';
          });
        }
      }

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
      var successMessage = @json($successMessage);

      if (!successMessage || successMessage.indexOf('Agendamento criado com sucesso') === -1) {
        return;
      }

      try {
        var AudioContextClass = window.AudioContext || window.webkitAudioContext;

        if (!AudioContextClass) {
          return;
        }

        var audioContext = new AudioContextClass();
        var now = audioContext.currentTime;

        [523.25, 659.25, 783.99].forEach(function (frequency, index) {
          var oscillator = audioContext.createOscillator();
          var gainNode = audioContext.createGain();

          oscillator.type = 'sine';
          oscillator.frequency.setValueAtTime(frequency, now + (index * 0.12));
          gainNode.gain.setValueAtTime(0.0001, now + (index * 0.12));
          gainNode.gain.exponentialRampToValueAtTime(0.08, now + (index * 0.12) + 0.02);
          gainNode.gain.exponentialRampToValueAtTime(0.0001, now + (index * 0.12) + 0.18);

          oscillator.connect(gainNode);
          gainNode.connect(audioContext.destination);
          oscillator.start(now + (index * 0.12));
          oscillator.stop(now + (index * 0.12) + 0.2);
        });
      } catch (error) {
        // Ignora falhas silenciosamente para não interferir no fluxo da página.
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
    document.addEventListener('DOMContentLoaded', function () {
      Array.from(document.querySelectorAll('label')).forEach(function (label) {
        Array.from(label.childNodes).forEach(function (node) {
          if (node.nodeType === Node.TEXT_NODE && node.textContent.trim() !== '') {
            node.textContent = node.textContent.replace(/\s*\*\s*$/u, '');
          }
        });
      });
    });
  </script>

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
