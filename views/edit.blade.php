@extends('themes::layouts.default')

@section('content')

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

    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Data Table With Full Features</h3>
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div style="float: left; width: 82%">
                            {!! Flysap\FileManager\editFile($pathModule . '/module.json', ['editor_var' => 'coreEditor']) !!}
                            <br /><input class="btn btn-flat js-update-file" type="button" value="{{ _('Update file') }}">
                        </div>

                        <div style="margin-left: 83%">
                            <div class="form-group">
                                <label>{{_("Select file")}}</label>
                                {!! Flysap\FileManager\listFiles($pathModule, 'select', ['active' => 'module.json', 'class' => 'form-control']) !!}
                            </div>
                            <input type="button" class="btn btn-flat js-load-file" value="{{ _('Load file') }}">
                        </div>

                        <div class="clearfix"></div>
                    </div><!-- /.box-body -->
                </div><!-- /.box -->
            </div><!-- /.col -->
        </div><!-- /.row -->

    </section><!-- /.content -->

    <script type="text/javascript">

        /**
         * Update file .
         */
        $(".js-update-file").on("click", function() {
            var file_active = '{{$pathModule}}/' + $("select.form-control option:selected").text();

            $.post('{{route('update-file')}}', {
                content: window.coreEditor.getDoc().getValue(),
                file: file_active
            });

            return false;
        });

        /**
         * Get the file .
         */
        $(".js-load-file").on("click", function() {
            var file_active = '{{$pathModule}}/' + $("select.form-control option:selected").text();

            $.get('{{route('load-file')}}', {
                file: file_active
            }, function(content) {
                window.coreEditor.getDoc().setValue(content)
            });

            return false;
        })
    </script>
@endsection