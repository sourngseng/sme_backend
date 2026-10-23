@extends('layouts.master')

@section('portal_title', 'SchoolMeal')
@section('portal_subtitle', 'Management Portal')

@section('sidebar_menu')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-item active">
        <div class="sidebar-item-left">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>{{ transLabel('menu.dashboard', 'Dashboard') }}</span>
        </div>
    </a>
    <a href="javascript:void(0)" class="sidebar-item">
        <div class="sidebar-item-left">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m4 6 8-4 8 4"/><path d="M18 10 22 12v10H2V12l4-2"/><path d="M6 5v17"/><path d="M18 5v17"/></svg>
            <span>{{ transLabel('menu.schools', 'Schools') }}</span>
        </div>
    </a>
    <a href="javascript:void(0)" class="sidebar-item">
        <div class="sidebar-item-left">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            <span>{{ transLabel('menu.restaurants', 'Restaurants') }}</span>
        </div>
    </a>
    <a href="javascript:void(0)" class="sidebar-item">
        <div class="sidebar-item-left">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>
            <span>{{ transLabel('menu.orders', 'Meal Orders') }}</span>
        </div>
    </a>
    <a href="javascript:void(0)" class="sidebar-item">
        <div class="sidebar-item-left">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
            <span>{{ transLabel('menu.delivery_drivers', 'Delivery Fleet') }}</span>
        </div>
    </a>
    <a href="javascript:void(0)" class="sidebar-item">
        <div class="sidebar-item-left">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
            <span>{{ transLabel('menu.settlements', 'Settlements') }}</span>
        </div>
    </a>
@endsection
