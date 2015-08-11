@extends('themes::pages.simple_table')

@section('header')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>

            <div class="btn-group">
                <button type="button" class="btn btn-default">Action</button>
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="caret"></span>
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="/module-manager/upload">{{_('Install')}}</a></li>
                </ul>
            </div>

            &nbsp;

            {{_('Module Manager')}}  <small>{{_("manage your modules easy")}}</small>
        </h1>

        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
            <li><a href="#">Tables</a></li>
            <li class="active">Simple</li>
        </ol>
    </section>
@endsection