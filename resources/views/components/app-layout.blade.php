@props(['title' => 'Dashboard', 'breadcrumb' => null])

@include('layouts.app', ['title' => $title, 'breadcrumb' => $breadcrumb, 'slot' => $slot])
