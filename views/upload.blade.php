<form enctype="multipart/form-data" method="post" action="{{route('module-upload')}}">
    <input type="file" name="module">

    <input type="hidden" name="_token" value="{{csrf_token()}}">
    <input type="submit" value="Upload">
</form>