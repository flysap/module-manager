<a href="{{route('module-upload')}}">{{_("Upload one")}}</a>

<table>
    <thead>
    <tr>
        <td>{{_("Name")}}</td>
        <td>{{_("Version")}}</td>
        <td>{{_("Is active")}}</td>
    </tr>
    </thead>

    @foreach($modules as $module)
        <tr>
            <td>{{$module->name}}</td>
            <td>{{$module->version}}</td>
            <td>{{$module->is_active}}</td>
        </tr>
    @endforeach
</table>