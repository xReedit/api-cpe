@php
    $path = explode('/', request()->path());
    $path[1] = (array_key_exists(1, $path)> 0)?$path[1]:'';
    $path[2] = (array_key_exists(2, $path)> 0)?$path[2]:'';
@endphp
<header class="header">
    <nav class="navbar">
        <div class="navbar-brand">
            <img src="{{ asset('images/logo-cpe.png') }}">
        </div>
        <div class="navbar-menu">
            @if (auth()->user()->isRole('super'))
            <a class="navbar-item {{ ($path[0] === 'clients')?'is-active':'' }}" href="{{ route('clients.index') }}">@lang('app.menu.clients')</a>
            <a class="navbar-item {{ ($path[0] === 'documents')?'is-active':'' }}" href="{{ route('documents.index') }}">@lang('app.menu.documents')</a>
            <a class="navbar-item {{ ($path[0] === 'summaries')?'is-active':'' }}" href="{{ route('summaries.index') }}">@lang('app.menu.summaries')</a>
            <a class="navbar-item {{ ($path[0] === 'summaries_annulment')?'is-active':'' }}" href="{{ route('summaries_annulment.index') }}">@lang('app.menu.summaries_annulment')</a>
            <a class="navbar-item {{ ($path[0] === 'annulments')?'is-active':'' }}" href="{{ route('annulments.index') }}">@lang('app.menu.annulments')</a>
            @endif
        </div>
    </nav>
</header>