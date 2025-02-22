{{-- @php
$navlinkActive = \Illuminate\Support\Str::startsWith(request()->url(), $to ?? '#') ? 'bg-gray-700 text-gray-200' : 'text-gray-300';
@endphp

<a 
	href="{{ $to ?? '#' }}" 
	{{ $attributes->merge([
		'class' => 'mb-1 rounded-lg px-2 py-1 block hover:bg-gray-700 transition duration-200 ease-in-out '. $navlinkActive
	]) }}>
	{{ $slot }}
</a> --}}

@php
$navlinkActive = \Illuminate\Support\Str::startsWith(request()->url(), $to ?? '#') ? 'bg-gray-700 text-gray-200' : 'text-gray-300';
$marginClass = $noMargin ?? false ? 'mb-0' : 'mb-1';
@endphp

<a 
    href="{{ $to ?? '#' }}" 
    {{ $attributes->merge([
        'class' => 'rounded-lg px-2 py-1 block hover:bg-gray-700 transition duration-200 ease-in-out ' . $navlinkActive . ' ' . $marginClass
    ]) }}>
    {{ $slot }}
</a>