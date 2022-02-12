@extends('layouts.app')

@section('content')
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <title>Laravel Project Manager</title>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="app-url" content="{{ url('/') }}">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
            integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    </head>

    <body>

        <div class="container">
            <h2 class="text-center mt-5 mb-3">Laravel Project Manager</h2>
            <div class="card">
                <div class="card-header">
                    <button class="btn btn-outline-primary" onclick="createProject()">
                        Create New Project
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Type a country name</label>
                        <input type="text" name="country" id="country" placeholder="Enter country name" class="form-control">
                    </div>
                    <div id="alert-div">
                    </div>
                    <table class="table table-bordered table-sm">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th width="280px">Action</th>
                            </tr>
                        </thead>
                        <tbody id="bodyData">

                        </tbody>
                        </table>
                </div>
            </div>
        </div>

        <!-- modal for creating and editing function -->
        <div class="modal" tabindex="-1" id="form-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Project Form</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="error-div"></div>

                        <form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="update_id" id="update_id">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" class="form-control" id="name" name="name">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" rows="3" name="description"></textarea>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <strong>Image:</strong>
                                    <input type="file" name="image" id="image" class="form-control" placeholder="image">
                                </div>
                            </div>


                            <input type="submit" class="btn btn-outline-primary mt-3" class="btnSubmit" />

                            <ul>

                            </ul>
                            <div id="alert-div1">

                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>


        <!-- view record modal -->
        <div class="modal" tabindex="-1" id="view-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                
                    <div class="modal-header">
                        <h5 class="modal-title">Project Information</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                  
                    <div class="modal-body">
                        <b>Name:</b>
                        <p id="name-info"></p>
                        <b>Description:</b>
                        <p id="description-info"></p>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            showAllProjects();
            $(document).ready(function(e) {
                let url = $('meta[name=app-url]').attr("content") + "/projects";

                $("#uploadForm").on('submit', (function(e) {
                    event.preventDefault();
                    if ($("#update_id").val() == null || $("#update_id").val() == "") {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: url,
                            type: "POST",
                            data: new FormData(this),
                            contentType: false,
                            cache: false,
                            processData: false,
                            success: function(data) {
                                $("#save-project-btn").prop('disabled', false);
                                let successHtml =
                                    '<div class="alert alert-success" role="alert"><b>Project Created Successfully</b></div>';
                                $("#alert-div").html(successHtml);
                                $("#name").val("");
                                $("#description").val("");
                                $("#bodyData").empty();
                                showAllProjects();
                                $("#form-modal").modal('hide');
                            },
                            error: function(response) {
                              //  $("#save-project-btn").prop('disabled', false);
                                validation(response);
                            }
                        });
                    } else {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            url: "update/" + $("#update_id").val(),
                            type: "POST",
                            data: new FormData(this),
                            contentType: false,
                            cache: false,
                            processData: false,
                            success: function(response) {
                                $("#save-project-btn").prop('disabled', false);
                                let successHtml =
                                    '<div class="alert alert-success" role="alert"><b>Project Updated Successfully</b></div>';
                                $("#alert-div").html(successHtml);
                                $("#name").val("");
                                $("#description").val("");
                                $("#bodyData").empty();
                                showAllProjects();
                                $("#form-modal").modal('hide');
                            },
                            error: function(response) {
                              validation(response);
                            }
                        });
                    }
                }));
            });


            function validation(response){
                if (typeof response.responseJSON.errors !== 'undefined') {
                                    console.log(response)
                                    let errors = response.responseJSON.errors;
                                    let descriptionValidation = "";
                                    if (typeof errors.description !== 'undefined') {
                                        descriptionValidation = '<li>' + errors.description[0] +
                                            '</li>';
                                    }
                                    let nameValidation = "";
                                    if (typeof errors.name !== 'undefined') {
                                        nameValidation = '<li>' + errors.name[0] + '</li>';
                                    }
                                    let ImageValidation = "";
                                    if (typeof errors.image !== 'undefined') {
                                        ImageValidation = '<li>' + errors.image[0] + '</li>';
                                    }

                                    let errorHtml =
                                        '<div class="alert alert-danger" role="alert">' +
                                        '<b>Validation Error!</b>' +
                                        '<ul>' + ImageValidation + nameValidation +
                                        descriptionValidation + '</ul>' +
                                        '</div>';
                                    $("#error-div").html(errorHtml);
                                }
            }

            function showAllProjects() {
                let url = $('meta[name=app-url]').attr("content") + "/projects";
                $.ajax({
                    url: "/all",
                    type: "GET",
                    cache: false,
                    dataType: 'json',
                    success: function(dataResult) {
                        console.log(dataResult);
                        var resultData = dataResult.data;
                        var bodyData = '';
                        var i = 1;
                        let editBtn;
                        $.each(resultData, function(index, row) {
                            var editUrl = url + '/' + row.id + "/edit";
                            let showBtn = '<button ' +
                                ' class="btn btn-outline-success" ' +
                                ' onclick="showProject(' + row.id + ')">Show' +
                                '</button> ';

                            let editBtn = '<button ' +
                                ' class="btn btn-outline-success" ' +
                                ' onclick="editProject(' + row.id + ')">Edit' +
                                '</button> ';
                            let deleteBtn = '<button ' +
                                ' class="btn btn-outline-danger" ' +
                                ' onclick="destroyProject(' + row.id + ')">Delete' +
                                '</button>';

                            bodyData += "<tr>"
                            bodyData += "<td>" + i++ + "</td><td>" + row.name + "</td><td>" + row
                                .description + "</td>" +
                                "<td>" + showBtn + editBtn + deleteBtn + "</td>"
                            bodyData += "</tr>";
                        })
                        $("#bodyData").append(bodyData);
                    }
                });
            }

            $(document).ready(function () {
             $('#country').on('keyup',function() {
                 var query = $(this).val(); 
                let url = $('meta[name=app-url]').attr("content") + "/projects";
                $.ajax({
                    url: "{{ route('searchData') }}",
                    type: "GET",
                    cache: false,
                    data:{'name':query},
                    dataType: 'json',
                    success: function(dataResult) {
                        console.log(dataResult);
                        var resultData = dataResult.data;
                        var bodyData = '';
                        var i = 1;
                        let editBtn;
                        $.each(resultData, function(index, row) {
                          
                            var editUrl = url + '/' + row.id + "/edit";
                            let showBtn = '<button ' +
                                ' class="btn btn-outline-success" ' +
                                ' onclick="showProject(' + row.id + ')">Show' +
                                '</button> ';

                            let editBtn = '<button ' +
                                ' class="btn btn-outline-success" ' +
                                ' onclick="editProject(' + row.id + ')">Edit' +
                                '</button> ';
                            let deleteBtn = '<button ' +
                                ' class="btn btn-outline-danger" ' +
                                ' onclick="destroyProject(' + row.id + ')">Delete' +
                                '</button>';

                            bodyData += "<tr>"
                            bodyData += "<td>" + i++ + "</td><td>" + row.name + "</td><td>" + row
                                .description + "</td>" +
                                "<td>" + showBtn + editBtn + deleteBtn + "</td>"
                            bodyData += "</tr>";
                            $("#bodyData").html("");
                        })
                        $("#bodyData").append(bodyData);
                       
                    }
                });
            });
        });
            function createProject() {
                $("#alert-div").html("");
                $("#error-div").html("");
                $("#update_id").val("");
                $("#name").val("");
                $("#description").val("");
                $("#form-modal").modal('show');
            }

            /*
                submit the form and will be stored to the database
            */
            function editProject(id) {
                $.ajax({
                    url: "/show/" + id,
                    type: "GET",
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'json',
                    success: function(info) {
                        $("#alert-div").html("");
                        $("#error-div").html("");
                        $("#update_id").val(info.singleInfo.id);
                        $("#name").val(info.singleInfo.name);
                        $("#description").val(info.singleInfo.description);
                        $("#form-modal").modal('show');
                    },
                    error: function(response) {
                        console.log(response.responseJSON)
                    }
                });
            }
            /*
                get and display the record info on modal
            */
            function showProject(id) {
                $("#name-info").html("");
                $("#description-info").html("");
                $.ajax({
                    url: "/show/" + id,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(info) {
                        $("#name-info").html(info.singleInfo.name);
                        $("#description-info").html(info.singleInfo.description);
                        $("#view-modal").modal('show');
                    },
                    error: function(response) {
                        console.log(response.responseJSON)
                    }
                });
            }
            /*
                delete record function
            */
            function destroyProject(id) {
                let url = $('meta[name=app-url]').attr("content") + "/projects/" + id;
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: "DELETE",
                    success: function(response) {
                        let successHtml =
                            '<div class="alert alert-success" role="alert"><b>Data Deleted Successfully</b></div>';
                        $("#alert-div").html(successHtml);
                        $("#bodyData").empty();
                        showAllProjects();
                    },
                    error: function(response) {
                        console.log(response.responseJSON)
                    }
                });
            }
        </script>
    </body>

    </html>
@endsection
