<a href="{{route('module-upload')}}">{{_("Upload one")}}</a>

<table>
    <thead>
    <tr>
        <td>{{_("Name")}}</td>
        <td>{{_("Description")}}</td>
        <td>{{_("Version")}}</td>
    </tr>
    </thead>

    @foreach($modules as $module)
        <tr>
            <td>{{$module['name']}}</td>
            <td>{{$module['description']}}</td>
            <td>{{$module['version']}}</td>
            <td colspan="2">
                <a href="{{route('module-remove', str_replace('/', '-', $module['name']))}}">rm</a>
            </td>
        </tr>
    @endforeach
</table>