<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload File</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container mt-5" style="max-width: 500px">

    <div class="alert alert-warning mb-4 text-center">
        <h2 class="display-6">Upload File</h2>
    </div>

    <div id="uploadError"> </div>
    <form id="fileUploadForm" method="POST" action="{{ url('/upload-doc-file') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-3">
            <input name="file" type="file" class="form-control" required>
        </div>
        <div class="d-grid mb-3">
            <input type="submit" value="Submit" class="btn btn-primary">
        </div>
        <div class="form-group">
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
            </div>
        </div>
    </form>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>
<script>
    $(function () {
        $(document).ready(function () {
            $(".progress").hide();
            $('#fileUploadForm').ajaxForm({
                beforeSend: function () {
                    $(".progress").show();
                    var percentage = '0';
                },
                uploadProgress: function (event, position, total, percentComplete) {
                    var percentage = percentComplete;
                        $(".progress-bar").css("width", percentage + "%").text(percentage + "%");
                },
                complete: function (data) {
                    $(".progress").hide();
                    var msg = data.responseJSON.message;
                    var status = (data.responseJSON.status === 'success') ? 'alert-success' : 'alert-danger';
                    let contents = '<div class="alert '+status+' alert-block">'+
                        '<strong>'+msg+'</strong></div>';
                    $('#uploadError').html(contents);

                    setTimeout(function () {
                        contents = '';
                        $('#uploadError').html(contents);
                        $('.progress .progress-bar').css("width", '0%', function() {
                            return $(this).attr("aria-valuenow", 0) + "%";
                        })
                    }, 5000)
                }
            });
        });
    });
</script>
</body>
</html>
