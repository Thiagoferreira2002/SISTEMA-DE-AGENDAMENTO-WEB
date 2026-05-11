@extends('admin.layouts.master')

@section('content')
<style>
    .settings-base-card {
        border-radius: 18px;
        overflow: hidden;
    }

    .settings-base-card .card-icon {
        margin: 14px 14px 0;
    }

    .settings-base-card .card-wrap {
        padding: 14px 14px 16px !important;
    }

    .settings-base-card .card-header h4 {
        font-size: 11px;
        line-height: 1.25;
        white-space: normal;
        margin-bottom: 0;
    }

    .settings-base-card .card-body {
        padding: 0 !important;
        margin-top: 10px;
        font-size: 13px !important;
        line-height: 1.45 !important;
    }

    .settings-base-card .settings-base-card-action {
        padding-top: 12px;
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Cadastros Base</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('admin.dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Cadastros Base</div>
        </div>
    </div>

    <div class="section-body">
        <div class="row">
            @foreach($cards as $card)
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card card-statistic-1 h-100 settings-base-card">
                        <div class="card-icon bg-primary">
                            <i class="{{ $card['icon'] }}"></i>
                        </div>
                        <div class="card-wrap w-100">
                            <div class="card-header"><h4>{{ $card['title'] }}</h4></div>
                            <div class="card-body text-muted" style="font-size: 14px; line-height: 1.5;">
                                {{ $card['description'] }}
                            </div>
                            <div class="settings-base-card-action">
                                <a href="{{ $card['route'] }}" class="btn btn-primary btn-sm">Acessar</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
