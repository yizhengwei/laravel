<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>

</head>
<body>
<div class="container">
    <div class="panel-heading">上传文件</div>
    <form class="form-horizontal" method="POST" action="/upload" enctype="multipart/form-data">
        {{ csrf_field() }}
        <label for="file">选择文件</label>
        <input id="file" type="file" class="form-control" name="source" required>
        <button type="submit" class="btn btn-primary">确定</button>
    </form>
</div>

</body>

