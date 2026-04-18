@extends('admin.layouts.master')

@section('content')
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
                    <div class="card card-statistic-1 h-100">
                        <div class="card-icon bg-primary">
                            <i class="{{ $card['icon'] }}"></i>
                        </div>
                        <div class="card-wrap w-100 pr-4 py-3">
                            <div class="card-header"><h4>{{ $card['title'] }}</h4></div>
                            <div class="card-body text-muted" style="font-size: 14px; line-height: 1.5;">
                                {{ $card['description'] }}
                            </div>
                            <div class="px-4 pb-4">
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
