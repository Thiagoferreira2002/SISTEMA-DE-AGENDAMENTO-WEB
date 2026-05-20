@extends('admin.layouts.master')
@section('content')
<style>
  .dashboard-shell {
    --dashboard-accent: #1E90FF;
    --dashboard-accent-deep: #1E90FF;
    --dashboard-dark: #123a63;
    --dashboard-soft: #f4f9ff;
    --dashboard-soft-border: rgba(30, 144, 255, 0.22);
    padding: 28px;
    border-radius: 28px;
    background:
      radial-gradient(circle at top right, rgba(30, 144, 255, 0.18), transparent 26%),
      linear-gradient(180deg, rgba(30, 144, 255, 0.08) 0%, rgba(244, 249, 255, 0.94) 42%, rgba(238, 245, 255, 0.98) 100%);
  }

  html[data-theme="dark"] .dashboard-shell {
    --dashboard-dark: #eef5fc;
    --dashboard-soft: #16283b;
    --dashboard-soft-border: rgba(143, 197, 255, 0.16);
    background:
      radial-gradient(circle at top right, rgba(118, 187, 255, 0.18), transparent 28%),
      linear-gradient(180deg, rgba(18, 35, 54, 0.98) 0%, rgba(16, 29, 42, 0.98) 100%);
    box-shadow: inset 0 0 0 1px rgba(143, 197, 255, 0.08);
  }

  .dashboard-shell .section-header {
    align-items: stretch;
    margin-bottom: 0;
  }

  .dashboard-shell .section-header h1 {
    color: var(--dashboard-dark);
    font-weight: 800;
  }

  .dashboard-hero {
    position: relative;
    overflow: hidden;
    border: 1px solid #d2dbe6 !important;
    border-radius: 22px;
    background: linear-gradient(135deg, rgba(30, 144, 255, 0.78) 0%, #1E90FF 100%);
    color: #ffffff;
    box-shadow:
      inset 0 0 0 1px #d2dbe6,
      0 18px 40px rgba(30, 144, 255, 0.16);
    animation: dashboardFadeIn .7s ease-out both;
  }

  .dashboard-hero::after {
    content: '';
    position: absolute;
    inset: auto -10% -45% auto;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.22);
    filter: blur(4px);
  }

  .dashboard-hero .card-body {
    position: relative;
    z-index: 1;
    padding: 32px;
  }

  .dashboard-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 999px;
    background: #1E90FF;
    color: #ffffff;
    font-size: 12px;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .dashboard-hero h2 {
    margin: 18px 0 10px;
    font-size: 2rem;
    font-weight: 700;
  }

  .dashboard-hero p {
    max-width: 720px;
    margin-bottom: 0;
    color: #000000;
  }

  html[data-theme="dark"] .dashboard-hero p {
    color: #ffffff;
  }

  html[data-theme="light"] .dashboard-kicker,
  body:not([data-theme="dark"]) .dashboard-kicker {
    background: #1E90FF;
    color: #ffffff;
  }

  .dashboard-metric-card,
  .dashboard-list-card {
    border: 1px solid #d2dbe6 !important;
    border-radius: 18px;
    box-shadow:
      inset 0 0 0 1px #d2dbe6,
      0 14px 28px rgba(30, 144, 255, 0.08);
    animation: dashboardRise .55s ease-out both;
  }

  html[data-theme="dark"] .dashboard-hero {
    border-color: #000000 !important;
    box-shadow:
      inset 0 0 0 1px #000000,
      0 18px 40px rgba(30, 144, 255, 0.16);
  }

  html[data-theme="dark"] .dashboard-metric-card,
  html[data-theme="dark"] .dashboard-list-card {
    border-color: #000000 !important;
    box-shadow:
      inset 0 0 0 1px #000000,
      0 14px 28px rgba(30, 144, 255, 0.08);
  }

  .dashboard-metric-card {
    overflow: hidden;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.99) 0%, rgba(244, 249, 255, 0.98) 100%);
    border-top: 4px solid var(--dashboard-accent-deep);
    height: 100%;
  }

  html[data-theme="dark"] .dashboard-metric-card {
    background: linear-gradient(180deg, rgba(23, 40, 59, 0.98) 0%, rgba(18, 33, 49, 0.98) 100%);
    box-shadow: 0 18px 34px rgba(2, 8, 15, 0.34);
  }

  .dashboard-metric-link {
    display: block;
    height: 100%;
    color: inherit;
    text-decoration: none;
  }

  .dashboard-metric-link:hover {
    color: inherit;
    text-decoration: none;
  }

  .dashboard-metric-link:hover .dashboard-metric-card {
    transform: translateY(-2px);
    box-shadow: 0 18px 34px rgba(30, 144, 255, 0.14);
  }

  .dashboard-metric-card .card-body {
    padding: 22px;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
  }

  .dashboard-metric-card .metric-icon {
    width: 44px;
    height: 44px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
    margin-bottom: 16px;
    margin-left: auto;
    background: linear-gradient(135deg, #0d3358 0%, #176fbe 100%);
    color: #ffffff;
    font-size: 18px;
    line-height: 1;
    text-align: center;
    box-shadow: 0 10px 20px rgba(13, 51, 88, 0.18);
  }

  html[data-theme="dark"] .dashboard-metric-card .metric-icon {
    background: linear-gradient(135deg, #0d3358 0%, #176fbe 100%);
    color: #eef5fc;
  }

  .dashboard-metric-card .metric-icon i,
  .dashboard-metric-card .metric-icon svg {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    margin: 0 auto;
  }

  .dashboard-metric-card h4 {
    color: #4d6d8a;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .04em;
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  html[data-theme="dark"] .dashboard-metric-card h4,
  html[data-theme="dark"] .dashboard-table thead th {
    color: #a9c5df;
  }

  .dashboard-metric-card .metric-value {
    color: var(--dashboard-dark);
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
  }

  html[data-theme="dark"] .dashboard-metric-card .metric-value {
    color: #f4f8fc;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.24);
  }

  .dashboard-metric-card .metric-footnote {
    margin-top: 10px;
    margin-top: auto;
    color: #587693;
    font-size: 13px;
  }

  html[data-theme="dark"] .dashboard-metric-card .metric-footnote {
    color: #bfd0e0;
  }

  .dashboard-list-card .card-header {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 20px 24px 0;
    background: linear-gradient(180deg, rgba(30, 144, 255, 0.12) 0%, rgba(30, 144, 255, 0) 100%);
    border-bottom: 0;
    position: relative;
  }

  .dashboard-list-card .card-header-action {
    margin-left: 0;
    width: auto !important;
    flex: 0 0 auto;
    align-self: center;
    position: absolute;
    right: 24px;
    top: 20px;
  }

  .dashboard-list-card .card-header-action .btn {
    width: auto !important;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
  }

  .dashboard-list-card {
    background: rgba(255, 255, 255, 0.98);
    border-top: 4px solid var(--dashboard-accent);
  }

  html[data-theme="dark"] .dashboard-list-card {
    background: linear-gradient(180deg, rgba(22, 40, 59, 0.98) 0%, rgba(19, 33, 49, 0.98) 100%);
    box-shadow: 0 18px 34px rgba(2, 8, 15, 0.34);
  }

  .dashboard-list-card .card-header h4 {
    color: var(--dashboard-dark);
    font-weight: 700;
    flex: 1 1 auto;
    text-align: center;
    margin-bottom: 0;
  }

  .dashboard-list-card .card-body {
    padding: 20px 24px 24px;
  }

  .dashboard-table thead th {
    border-top: 0;
    color: #ffffff;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: .04em;
    background: #1E90FF;
    border-bottom: 0;
    text-align: center;
  }

  .dashboard-table tbody tr {
    transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease;
  }

  .dashboard-table tbody tr:hover {
    transform: translateY(-1px);
    box-shadow: inset 0 0 0 9999px rgba(30, 144, 255, 0.1);
  }

  .dashboard-appointment-row {
    cursor: pointer;
  }

  .dashboard-appointment-row:focus-visible {
    outline: 2px solid #1E90FF;
    outline-offset: -2px;
  }

  .dashboard-table-responsive {
    overflow-x: auto;
  }

  .dashboard-table {
    min-width: 860px;
  }

  .dashboard-table td {
    vertical-align: middle;
    text-align: center;
  }

  .dashboard-table td:nth-child(1) {
    min-width: 230px;
    text-align: left;
  }

  .dashboard-table td:nth-child(2) {
    min-width: 180px;
    white-space: normal;
  }

  .dashboard-table td:nth-child(3) {
    min-width: 170px;
    white-space: normal;
  }

  html[data-theme="dark"] .dashboard-table tbody tr:hover {
    box-shadow: inset 0 0 0 9999px rgba(118, 187, 255, 0.08);
  }

  .dashboard-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 120px;
    padding: 7px 12px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 12px;
  }

  .dashboard-status.confirmado {
    background: rgba(46, 204, 113, 0.18);
    color: #198754;
  }

  .dashboard-status.pendente {
    background: rgba(255, 193, 7, 0.2);
    color: #a56a00;
  }

  .dashboard-status.cancelado {
    background: rgba(30, 144, 255, 0.14);
    color: #155a9d;
  }

  .dashboard-patient-cell {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    min-width: 0;
  }

  .dashboard-patient-cell img {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(23, 111, 190, 0.12);
    flex: 0 0 auto;
  }

  .dashboard-patient-copy {
    min-width: 0;
    text-align: left;
  }

  .dashboard-patient-copy strong {
    display: block;
    color: var(--dashboard-dark);
    font-size: 14px;
    line-height: 1.3;
  }

  .dashboard-patient-copy small {
    display: none;
    margin-top: 4px;
    color: #5b7895;
    line-height: 1.3;
  }

  html[data-theme="dark"] .dashboard-patient-copy strong {
    color: #eef5fc;
  }

  html[data-theme="dark"] .dashboard-patient-copy small {
    color: #a9c5df;
  }

  .dashboard-shell .btn-primary {
    background: #1E90FF;
    border: 0;
    color: #ffffff;
    box-shadow: 0 10px 22px rgba(30, 144, 255, 0.2);
  }

  .dashboard-shell .btn-primary:hover,
  .dashboard-shell .btn-primary:focus {
    background: #1E90FF !important;
    color: #ffffff;
  }

  .dashboard-metrics-row {
    margin-top: 0;
  }

  .dashboard-secondary-row {
    justify-content: center;
    margin-top: 28px;
  }

  .dashboard-schedule-row {
    margin-top: 34px;
  }

  .dashboard-mobile-label {
    display: none;
  }

  .dashboard-details-modal {
    position: fixed;
    inset: 0;
    z-index: 1100;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
    background: rgba(7, 16, 28, 0.62);
    backdrop-filter: blur(4px);
  }

  .dashboard-details-modal.is-open {
    display: flex;
  }

  .dashboard-details-dialog {
    width: min(100%, 760px);
    max-height: calc(100vh - 36px);
    overflow: auto;
    border-radius: 24px;
    border: 1px solid #d2dbe6;
    background: linear-gradient(180deg, rgba(255, 255, 255, 0.99) 0%, rgba(244, 249, 255, 0.98) 100%);
    box-shadow: 0 24px 54px rgba(7, 16, 28, 0.2);
  }

  html[data-theme="dark"] .dashboard-details-dialog {
    border-color: #000000;
    background: linear-gradient(180deg, rgba(22, 40, 59, 0.99) 0%, rgba(18, 33, 49, 0.99) 100%);
  }

  .dashboard-details-header,
  .dashboard-details-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 20px 24px;
  }

  .dashboard-details-header {
    border-bottom: 1px solid rgba(30, 144, 255, 0.14);
  }

  .dashboard-details-footer {
    border-top: 1px solid rgba(30, 144, 255, 0.14);
  }

  .dashboard-details-header h4 {
    margin: 0;
    color: var(--dashboard-dark);
    font-weight: 700;
  }

  html[data-theme="dark"] .dashboard-details-header h4 {
    color: #eef5fc;
  }

  .dashboard-details-close {
    width: 40px;
    height: 40px;
    border: 0;
    border-radius: 999px;
    background: rgba(30, 144, 255, 0.12);
    color: #0f5aa6;
    font-size: 22px;
    line-height: 1;
  }

  .dashboard-details-close:hover {
    background: rgba(30, 144, 255, 0.18);
  }

  html[data-theme="dark"] .dashboard-details-close {
    background: rgba(143, 197, 255, 0.12);
    color: #d8ebff;
  }

  .dashboard-details-body {
    padding: 24px;
  }

  .dashboard-details-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
  }

  .dashboard-detail-card {
    padding: 16px;
    border-radius: 18px;
    border: 1px solid rgba(30, 144, 255, 0.14);
    background: rgba(255, 255, 255, 0.78);
  }

  html[data-theme="dark"] .dashboard-detail-card {
    border-color: rgba(143, 197, 255, 0.12);
    background: rgba(255, 255, 255, 0.04);
  }

  .dashboard-detail-card.dashboard-detail-card-full {
    grid-column: 1 / -1;
  }

  .dashboard-detail-card label {
    display: block;
    margin-bottom: 6px;
    color: #587693;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .dashboard-detail-card p {
    margin: 0;
    color: var(--dashboard-dark);
    font-size: 15px;
    font-weight: 600;
    line-height: 1.5;
  }

  html[data-theme="dark"] .dashboard-detail-card label {
    color: #a9c5df;
  }

  html[data-theme="dark"] .dashboard-detail-card p {
    color: #eef5fc;
  }

  .dashboard-detail-summary {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .dashboard-detail-summary img {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(23, 111, 190, 0.12);
    flex: 0 0 auto;
  }

  .dashboard-detail-summary strong {
    display: block;
    color: var(--dashboard-dark);
    font-size: 18px;
    line-height: 1.3;
  }

  .dashboard-detail-summary span {
    display: block;
    margin-top: 4px;
    color: #5b7895;
    font-size: 13px;
  }

  html[data-theme="dark"] .dashboard-detail-summary strong {
    color: #eef5fc;
  }

  html[data-theme="dark"] .dashboard-detail-summary span {
    color: #a9c5df;
  }

  .dashboard-modal-status {
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  @media (max-width: 991.98px) {
    .dashboard-metrics-row,
    .dashboard-secondary-row {
      row-gap: 28px;
    }

    .dashboard-metrics-row > [class*='col-'],
    .dashboard-secondary-row > [class*='col-'] {
      margin-bottom: 0;
    }

    .dashboard-metric-card {
      height: auto;
    }
  }

  @media (max-width: 767.98px) {
    .dashboard-shell {
      padding: 18px;
      border-radius: 20px;
    }

    .dashboard-hero {
      margin-bottom: 20px !important;
    }

    .dashboard-hero .card-body,
    .dashboard-list-card .card-body,
    .dashboard-list-card .card-header {
      padding-left: 18px;
      padding-right: 18px;
    }

    .dashboard-hero h2 {
      font-size: 1.6rem;
    }

    .dashboard-metric-card .metric-value {
      font-size: 1.55rem;
    }

    .dashboard-list-card .card-header-action,
    .dashboard-list-card .card-header-action .btn {
      width: auto;
    }

    .dashboard-list-card .card-header-action {
      align-self: flex-start;
    }

    .dashboard-list-card .card-header-action .btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 8px 14px;
      white-space: nowrap;
    }

    .dashboard-metrics-row > [class*='col-'],
    .dashboard-secondary-row > [class*='col-'] {
      margin-bottom: 20px;
    }

    .dashboard-metrics-row > [class*='col-']:last-child,
    .dashboard-secondary-row > [class*='col-']:last-child {
      margin-bottom: 0;
    }

    .dashboard-secondary-row {
      margin-top: 24px;
    }

    .dashboard-schedule-row {
      margin-top: 28px;
    }

    .dashboard-metric-card .card-body {
      padding: 18px 16px;
      min-height: 0;
      height: auto;
    }

    .dashboard-metric-card {
      min-height: 0;
      height: auto;
    }

    .dashboard-metric-card h4 {
      min-height: 32px;
      line-height: 1.25;
      font-size: 12px;
      margin-bottom: 6px;
    }

    .dashboard-metric-card .metric-footnote {
      min-height: 0;
      line-height: 1.35;
      font-size: 12px;
      margin-top: 4px;
    }

    .dashboard-metric-card .metric-icon {
      width: 50px;
      height: 50px;
      margin-bottom: 14px;
      font-size: 20px;
    }

    .dashboard-list-card {
      border-radius: 20px;
    }

    .dashboard-list-card .card-header {
      gap: 12px;
    }

    .dashboard-list-card .card-body {
      padding-top: 18px;
      padding-bottom: 20px;
    }

    .dashboard-mobile-label {
      display: block;
      margin-bottom: 6px;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: .08em;
      text-transform: uppercase;
      color: #7a95af;
      line-height: 1.1;
    }

    html[data-theme="dark"] .dashboard-mobile-label {
      color: #a9c5df;
    }

    .dashboard-table-responsive {
      overflow: visible;
    }

    .dashboard-table.dashboard-table-mobile {
      border: 0;
      min-width: 0;
    }

    .dashboard-patient-copy small {
      display: block;
    }

    .dashboard-table.dashboard-table-mobile thead {
      display: none;
    }

    .dashboard-table.dashboard-table-mobile tbody {
      display: grid;
      gap: 16px;
    }

    .dashboard-table.dashboard-table-mobile tbody tr {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      border: 1px solid var(--dashboard-soft-border);
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.98);
      box-shadow: 0 14px 28px rgba(30, 144, 255, 0.09);
      overflow: hidden;
    }

    html[data-theme="dark"] .dashboard-table.dashboard-table-mobile tbody tr {
      background: linear-gradient(180deg, rgba(22, 40, 59, 0.98) 0%, rgba(19, 33, 49, 0.98) 100%);
      box-shadow: 0 18px 34px rgba(2, 8, 15, 0.26);
    }

    .dashboard-table.dashboard-table-mobile tbody td {
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-width: 0;
      width: 100%;
      padding: 15px 16px !important;
      border: 0;
      border-top: 1px solid var(--dashboard-soft-border);
      text-align: left !important;
      white-space: normal;
      word-break: break-word;
      overflow-wrap: anywhere;
    }

    .dashboard-table.dashboard-table-mobile tbody tr > td:first-child,
    .dashboard-table.dashboard-table-mobile tbody tr > td:last-child,
    .dashboard-table.dashboard-table-mobile tbody tr > td.dashboard-mobile-full,
    .dashboard-table.dashboard-table-mobile tbody tr > td[colspan] {
      grid-column: 1 / -1;
    }

    .dashboard-table.dashboard-table-mobile tbody tr > td:nth-child(odd):not(:first-child):not(:last-child):not(.dashboard-mobile-full):not([colspan]) {
      border-right: 1px solid var(--dashboard-soft-border);
    }

    .dashboard-table.dashboard-table-mobile tbody tr > td:not(:last-child) {
      min-height: 72px;
    }

    .dashboard-table.dashboard-table-mobile tbody td[colspan] {
      text-align: center !important;
    }

    .dashboard-table.dashboard-table-mobile .dashboard-status {
      align-self: flex-start;
      min-width: 0;
      padding-left: 14px;
      padding-right: 14px;
    }

    .dashboard-details-grid {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 420px) {
    .dashboard-table.dashboard-table-mobile tbody tr {
      grid-template-columns: 1fr;
    }

    .dashboard-table.dashboard-table-mobile tbody tr > td:not(:last-child) {
      min-height: 0;
    }
  }

  @keyframes dashboardFadeIn {
    from { opacity: 0; transform: translateY(14px); }
    to { opacity: 1; transform: translateY(0); }
  }

  @keyframes dashboardRise {
    from { opacity: 0; transform: translateY(18px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>
 <section class="section">
  <div class="dashboard-shell">
    <div class="section-header border-0 mb-4">
      <h1>Página Inicial</h1>
    </div>

    <div class="card dashboard-hero mb-4">
      <div class="card-body">
        <span class="dashboard-kicker">
          <i class="fas fa-wave-square"></i>
          {{ ($isProfessionalDashboard ?? false) ? 'Visão do profissional' : 'Visão geral da clínica' }}
        </span>
        <h2>{{ ($isProfessionalDashboard ?? false) ? ($dashboardWelcome ?? 'Boas-vindas') : 'Painel de Controle' }}</h2>
        <p>{{ $dashboardSubtitle ?? 'Acompanhe os indicadores e os próximos atendimentos.' }}</p>
      </div>
    </div>

    <div class="row dashboard-metrics-row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ $dashboardLinks['total'] ?? route('admin.agendamentos.index') }}" class="dashboard-metric-link">
          <div class="card dashboard-metric-card" style="animation-delay:.05s;">
            <div class="card-body">
              <div class="metric-icon"><i class="far fa-calendar"></i></div>
              <h4>Total de Agendamentos</h4>
              <div class="metric-value">{{ $totalAgendamentos }}</div>
              <div class="metric-footnote">{{ ($isProfessionalDashboard ?? false) ? 'Mostra apenas os seus agendamentos ativos.' : 'Conta apenas agendamentos ativos, sem cancelados e sem finalizados.' }}</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ $dashboardLinks['pendentes'] ?? route('admin.agendamentos.confirmations') }}" class="dashboard-metric-link">
          <div class="card dashboard-metric-card" style="animation-delay:.12s;">
            <div class="card-body">
              <div class="metric-icon"><i class="far fa-clock"></i></div>
              <h4>Agendamentos Pendentes</h4>
              <div class="metric-value">{{ $agendamentosPendentes }}</div>
              <div class="metric-footnote">{{ ($isProfessionalDashboard ?? false) ? 'Mostra apenas pendências vinculadas ao seu perfil.' : 'Itens pendentes ou sem status, excluindo cancelados e finalizados.' }}</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ $dashboardLinks['confirmados'] ?? route('admin.agendamentos.index') }}" class="dashboard-metric-link">
          <div class="card dashboard-metric-card" style="animation-delay:.19s;">
            <div class="card-body">
              <div class="metric-icon"><i class="far fa-check-circle"></i></div>
              <h4>Agendamentos Confirmados</h4>
              <div class="metric-value">{{ $agendamentosConfirmados }}</div>
              <div class="metric-footnote">{{ ($isProfessionalDashboard ?? false) ? 'Mostra apenas confirmações vinculadas ao seu perfil.' : 'Somente agendamentos confirmados e ainda não concluídos.' }}</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ $dashboardLinks['atrasados'] ?? route('admin.doctor.pending-finalization') }}" class="dashboard-metric-link">
          <div class="card dashboard-metric-card" style="animation-delay:.26s;">
            <div class="card-body">
              <div class="metric-icon"><i class="fas fa-exclamation-triangle"></i></div>
              <h4>Atendimentos em Atraso</h4>
              <div class="metric-value">{{ $agendamentosEmAtraso ?? 0 }}</div>
              <div class="metric-footnote">
                {{ ($isProfessionalDashboard ?? false) ? 'Atendimentos do seu perfil cujo horário final já passou.' : 'Atendimentos ativos cujo horário final já foi ultrapassado.' }}
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="row dashboard-secondary-row">
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ $dashboardLinks['complementar'] ?? route('admin.patients.index') }}" class="dashboard-metric-link">
          <div class="card dashboard-metric-card" style="animation-delay:.26s;">
            <div class="card-body">
              <div class="metric-icon"><i class="far fa-user"></i></div>
              <h4>Total de Pacientes</h4>
              <div class="metric-value">{{ $totalPacientes }}</div>
              <div class="metric-footnote">Base total de pacientes cadastrados no sistema.</div>
            </div>
          </div>
        </a>
      </div>
      <div class="col-lg-3 col-md-6 col-sm-6 col-12">
        <a href="{{ $dashboardLinks['finalizados'] ?? route('admin.patients.history') }}" class="dashboard-metric-link">
          <div class="card dashboard-metric-card" style="animation-delay:.33s;">
            <div class="card-body">
              <div class="metric-icon"><i class="fas fa-check-double"></i></div>
              <h4>Serviços Finalizados</h4>
              <div class="metric-value">{{ $agendamentosFinalizados ?? 0 }}</div>
              <div class="metric-footnote">
                {{ ($isProfessionalDashboard ?? false) ? 'Total de atendimentos concluídos no seu perfil.' : 'Total de atendimentos concluídos na clínica.' }}
              </div>
            </div>
          </div>
        </a>
      </div>
    </div>

    <div class="row dashboard-schedule-row">
      <div class="col-lg-12 col-md-12 col-12 col-sm-12">
        <div class="card dashboard-list-card" style="animation-delay:.34s;">
          <div class="card-header">
            <h4>{{ ($isProfessionalDashboard ?? false) ? 'Seus Próximos Agendamentos' : 'Próximos Agendamentos' }}</h4>
            <div class="card-header-action">
              <a href="{{ ($isProfessionalDashboard ?? false) ? route('admin.doctor.queue') : route('admin.agendamentos.index') }}" class="btn btn-primary">Ver todos</a>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive dashboard-table-responsive">
              <table class="table dashboard-table dashboard-table-mobile mb-0">
                <thead>
                  <tr>
                    <th>Paciente</th>
                    <th>Serviço</th>
                    <th>Profissional</th>
                    <th>Data</th>
                    <th>Horário</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($proximosAgendamentos as $agendamento)
                    @php
                      $appointmentStatus = ucfirst($agendamento->status ?? 'pendente');
                      $appointmentDescription = $agendamento->motivo_consulta ?: ($agendamento->descricao ?: 'Sem descricao informada.');
                    @endphp
                    <tr
                      class="dashboard-appointment-row"
                      data-appointment-name="{{ e($agendamento->nome ?: 'Paciente') }}"
                      data-appointment-photo="{{ e($agendamento->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png')) }}"
                      data-appointment-service="{{ e($agendamento->servico ?: 'Atendimento') }}"
                      data-appointment-professional="{{ e($agendamento->professional->nome ?? $agendamento->medico ?? 'Nao informado') }}"
                      data-appointment-date="{{ e(optional($agendamento->data_agendamento)->format('d/m/Y') ?: '-') }}"
                      data-appointment-time="{{ e($agendamento->horario ?: '-') }}"
                      data-appointment-status="{{ e($appointmentStatus) }}"
                      data-appointment-status-class="{{ e($agendamento->status ?: 'pendente') }}"
                      data-appointment-email="{{ e($agendamento->email ?: '-') }}"
                      data-appointment-phone="{{ e($agendamento->telefone ?: '-') }}"
                      data-appointment-description="{{ e($appointmentDescription) }}"
                      tabindex="0"
                    >
                      <td class="dashboard-mobile-full" data-label="Paciente">
                        <span class="dashboard-mobile-label">Paciente</span>
                        <div class="dashboard-patient-cell">
                          <img src="{{ $agendamento->patient?->foto_url ?? asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto de {{ $agendamento->nome }}">
                          <div class="dashboard-patient-copy">
                            <strong>{{ $agendamento->nome }}</strong>
                            <small>{{ $agendamento->servico }}</small>
                          </div>
                        </div>
                      </td>
                      <td data-label="Serviço">
                        <span class="dashboard-mobile-label">Serviço</span>
                        {{ $agendamento->servico }}
                      </td>
                      <td data-label="Profissional">
                        <span class="dashboard-mobile-label">Profissional</span>
                        {{ $agendamento->professional->nome ?? $agendamento->medico ?? 'Não informado' }}
                      </td>
                      <td data-label="Data">
                        <span class="dashboard-mobile-label">Data</span>
                        {{ optional($agendamento->data_agendamento)->format('d/m/Y') }}
                      </td>
                      <td data-label="Horário">
                        <span class="dashboard-mobile-label">Horário</span>
                        {{ $agendamento->horario }}
                      </td>
                      <td class="dashboard-mobile-full" data-label="Status">
                        <span class="dashboard-mobile-label">Status</span>
                        <span class="dashboard-status {{ $agendamento->status }}">
                          {{ ucfirst($agendamento->status ?? 'pendente') }}
                        </span>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center text-muted py-4">Nenhum agendamento próximo encontrado.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<div class="dashboard-details-modal" id="dashboardAppointmentModal" aria-hidden="true">
  <div class="dashboard-details-dialog">
    <div class="dashboard-details-header">
      <h4>Detalhes do agendamento</h4>
      <button type="button" class="dashboard-details-close" data-dashboard-modal-close aria-label="Fechar">&times;</button>
    </div>
    <div class="dashboard-details-body">
      <div class="dashboard-details-grid">
        <div class="dashboard-detail-card dashboard-detail-card-full">
          <div class="dashboard-detail-summary">
            <img src="{{ asset('backend/assets/img/avatar/avatar-1.png') }}" alt="Foto do paciente" data-dashboard-modal-photo>
            <div>
              <strong data-dashboard-modal-name>-</strong>
              <span data-dashboard-modal-subtitle>Informacoes principais do atendimento</span>
            </div>
          </div>
        </div>
        <div class="dashboard-detail-card">
          <label>Servico</label>
          <p data-dashboard-modal-service>-</p>
        </div>
        <div class="dashboard-detail-card">
          <label>Profissional</label>
          <p data-dashboard-modal-professional>-</p>
        </div>
        <div class="dashboard-detail-card">
          <label>Data</label>
          <p data-dashboard-modal-date>-</p>
        </div>
        <div class="dashboard-detail-card">
          <label>Horario</label>
          <p data-dashboard-modal-time>-</p>
        </div>
        <div class="dashboard-detail-card">
          <label>Status</label>
          <p><span class="dashboard-status dashboard-modal-status pendente" data-dashboard-modal-status>-</span></p>
        </div>
        <div class="dashboard-detail-card">
          <label>Telefone</label>
          <p data-dashboard-modal-phone>-</p>
        </div>
        <div class="dashboard-detail-card">
          <label>E-mail</label>
          <p data-dashboard-modal-email>-</p>
        </div>
        <div class="dashboard-detail-card dashboard-detail-card-full">
          <label>Motivo do agendamento</label>
          <p data-dashboard-modal-description>-</p>
        </div>
      </div>
    </div>
    <div class="dashboard-details-footer">
      <span></span>
      <button type="button" class="btn btn-light" data-dashboard-modal-close>Fechar</button>
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    var appointmentRows = document.querySelectorAll('.dashboard-appointment-row');
    var modal = document.getElementById('dashboardAppointmentModal');
    var closeButtons = modal ? modal.querySelectorAll('[data-dashboard-modal-close]') : [];

    if (!appointmentRows.length || !modal) {
      return;
    }

    var fields = {
      photo: modal.querySelector('[data-dashboard-modal-photo]'),
      name: modal.querySelector('[data-dashboard-modal-name]'),
      subtitle: modal.querySelector('[data-dashboard-modal-subtitle]'),
      service: modal.querySelector('[data-dashboard-modal-service]'),
      professional: modal.querySelector('[data-dashboard-modal-professional]'),
      date: modal.querySelector('[data-dashboard-modal-date]'),
      time: modal.querySelector('[data-dashboard-modal-time]'),
      status: modal.querySelector('[data-dashboard-modal-status]'),
      phone: modal.querySelector('[data-dashboard-modal-phone]'),
      email: modal.querySelector('[data-dashboard-modal-email]'),
      description: modal.querySelector('[data-dashboard-modal-description]')
    };

    var closeModal = function () {
      modal.classList.remove('is-open');
      modal.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
    };

    appointmentRows.forEach(function (row) {
      row.addEventListener('click', function () {
        fields.photo.setAttribute('src', row.dataset.appointmentPhoto || '{{ asset('backend/assets/img/avatar/avatar-1.png') }}');
        fields.photo.setAttribute('alt', 'Foto de ' + (row.dataset.appointmentName || 'paciente'));
        fields.name.textContent = row.dataset.appointmentName || '-';
        fields.subtitle.textContent = row.dataset.appointmentService || 'Informacoes principais do atendimento';
        fields.service.textContent = row.dataset.appointmentService || '-';
        fields.professional.textContent = row.dataset.appointmentProfessional || '-';
        fields.date.textContent = row.dataset.appointmentDate || '-';
        fields.time.textContent = row.dataset.appointmentTime || '-';
        fields.phone.textContent = row.dataset.appointmentPhone || '-';
        fields.email.textContent = row.dataset.appointmentEmail || '-';
        fields.description.textContent = row.dataset.appointmentDescription || '-';
        fields.status.textContent = row.dataset.appointmentStatus || '-';
        fields.status.className = 'dashboard-status dashboard-modal-status ' + (row.dataset.appointmentStatusClass || 'pendente');

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
      });

      row.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          row.click();
        }
      });
    });

    closeButtons.forEach(function (button) {
      button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', function (event) {
      if (event.target === modal) {
        closeModal();
      }
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && modal.classList.contains('is-open')) {
        closeModal();
      }
    });
  });
</script>
@endsection
