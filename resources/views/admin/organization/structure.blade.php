@extends('layouts.app')

@section('title', 'Organization Structure')

@push('styles')
<style>
/* ORG CHART CSS TREE */
.tree ul {
    padding-top: 20px; position: relative;
    transition: all 0.5s;
    display: flex; justify-content: center;
}
.tree li {
    float: left; text-align: center;
    list-style-type: none;
    position: relative;
    padding: 20px 5px 0 5px;
    transition: all 0.5s;
}
/* Connecting lines */
.tree li::before, .tree li::after {
    content: ''; position: absolute; top: 0; right: 50%;
    border-top: 2px solid #ccc; width: 50%; height: 20px;
}
.tree li::after {
    right: auto; left: 50%; border-left: 2px solid #ccc;
}
.tree li:only-child::after, .tree li:only-child::before {
    display: none;
}
.tree li:only-child { padding-top: 0;}
.tree li:first-child::before, .tree li:last-child::after {
    border: 0 none;
}
.tree li:last-child::before{
    border-right: 2px solid #ccc; border-radius: 0 5px 0 0;
}
.tree li:first-child::after{
    border-radius: 5px 0 0 0;
}
.tree ul ul::before{
    content: ''; position: absolute; top: 0; left: 50%;
    border-left: 2px solid #ccc; width: 0; height: 20px;
}
/* Card Styles */
.node-card {
    border: 1px solid #ddd; padding: 10px 15px;
    text-decoration: none; color: #666;
    display: inline-block; border-radius: 5px;
    background: white; min-width: 150px;
    transition: all 0.5s;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.node-card:hover {
    background: #fdfdfd; border-color: #873260;
}
.node-header {
    background: #123456; color: white;
    padding: 5px 10px; border-radius: 4px 4px 0 0;
    font-weight: bold; font-size: 0.9rem;
}
.node-body {
    padding: 10px; font-size: 0.85rem;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4 overflow-auto">
    <h4 class="fw-bold mb-4" style="color: #123456;"><i class="bi bi-diagram-3 me-2"></i> Organization Structure</h4>

    <div class="tree">
        <ul>
            <li>
                {{-- ROOT NODE --}}
                <div class="node-card">
                    <div class="node-header">iManageHR</div>
                    <div class="node-body">
                        <strong>Headquarters</strong><br>
                        <span class="text-muted small">All Departments</span>
                    </div>
                </div>

                <ul>
                    @foreach($structure as $dept)
                    <li>
                        {{-- DEPARTMENT NODE --}}
                        <div class="node-card">
                            <div class="node-header">{{ $dept['department'] }}</div>
                            <div class="node-body text-start">
                                {{-- Supervisors --}}
                                @if($dept['supervisors']->count() > 0)
                                    <div class="mb-2 border-bottom pb-1">
                                        <strong class="text-dark d-block">Leads:</strong>
                                        @foreach($dept['supervisors'] as $sup)
                                            <div class="d-flex align-items-center mt-1">
                                                <i class="bi bi-person-badge-fill text-warning me-1"></i>
                                                <span>{{ $sup->name }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="mb-2 text-danger small">No Supervisor Assigned</div>
                                @endif

                                {{-- Employee Count --}}
                                <div class="text-muted small">
                                    <i class="bi bi-people-fill me-1"></i>
                                    {{ $dept['employees']->count() }} Staff Member(s)
                                </div>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </li>
        </ul>
    </div>
</div>
@endsection