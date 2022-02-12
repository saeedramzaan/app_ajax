<!DOCTYPE html>
<html lang="en">
<head>
    <title>Laravel Project Manager</title>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
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
                <div id="alert-div">
                 
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th width="240px">Action</th>
                        </tr>
                    </thead>
                    <tbody id="projects-table-body">
                         
                    </tbody>
                     
                </table>
            </div>
        </div>
    </div>
  
    <!-- modal for creating and editing function -->
    <div class="modal" tabindex="-1"  id="form-modal">
        <div class="modal-dialog" >
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Project Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="error-div"></div>
                <form>
                    <input type="hidden" name="update_id" id="update_id">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" rows="3" name="description"></textarea>
                    </div>
                 
                    <button type="submit" class="btn btn-outline-primary mt-3" id="save-project-btn">Save Project</button>
                </form>
            </div>
            </div>
        </div>
    </div>
 
  
    <!-- view record modal -->
    <div class="modal" tabindex="-1" id="view-modal">
        <div class="modal-dialog" >
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
  
     
     
        /*
            This function will get all the project records
        */
        function showAllProjects()
        {
            let url = $('meta[name=app-url]').attr("content") + "/projects";
            $.ajax({
                url: 'http://localhost:8000/all',
                type: "GET",
                success: function(response) {
                  
                    $("#projects-table-body").html("");
                   // console.log(response.projects);
                    var projects = response.projects;
                  //  console.log(projects);
                    for (var i = 0; i < projects.length; i++) 
                    {
                        let showBtn =  '<button ' +
                            ' class="btn btn-outline-info" ' +
                            ' onclick="showProject(' + projects[i].id + ')">Show' +
                        '</button> ';
                        let editBtn =  '<button ' +
                            ' class="btn btn-outline-success" ' +
                            ' onclick="editProject(' + projects[i].id + ')">Edit' +
                        '</button> ';
                        let deleteBtn =  '<button ' +
                            ' class="btn btn-outline-danger" ' +
                            ' onclick="destroyProject(' + projects[i].id + ')">Delete' +
                        '</button>';
     
                        let projectRow = '<tr>' +
                            '<td>' + projects[i].name + '</td>' +
                            '<td>' + projects[i].description + '</td>' +
                            '<td>' + showBtn + editBtn + deleteBtn + '</td>' +
                        '</tr>';
                        $("#projects-table-body").append(projectRow);
                    }
     
                     
                },
                error: function(response) {
                    console.log(response.responseJSON)
                }
            });
        }
     
        showAllProjects();
        /*
            check if form submitted is for creating or updating
        */
    
     
        /*
            show modal for creating a record and 
            empty the values of form and remove existing alerts
        */
        function createProject()
        {
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
        function storeProject()
        {   
            $("#save-project-btn").prop('disabled', true);
            let url = $('meta[name=app-url]').attr("content") + "/projects";
            let data = {
                name: $("#name").val(),
                description: $("#description").val(),
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "POST",
                data: data,
                success: function(response) {
                    $("#save-project-btn").prop('disabled', false);
                    let successHtml = '<div class="alert alert-success" role="alert"><b>Project Created Successfully</b></div>';
                    $("#alert-div").html(successHtml);
                    $("#name").val("");
                    $("#description").val("");
                    showAllProjects();
                    $("#form-modal").modal('hide');
                },
                error: function(response) {
                    $("#save-project-btn").prop('disabled', false);
     
                    /*
        show validation error
                    */
                    if (typeof response.responseJSON.errors !== 'undefined') 
                    {
        let errors = response.responseJSON.errors;
        let descriptionValidation = "";
        if (typeof errors.description !== 'undefined') 
                        {
                            descriptionValidation = '<li>' + errors.description[0] + '</li>';
                        }
                        let nameValidation = "";
        if (typeof errors.name !== 'undefined') 
                        {
                            nameValidation = '<li>' + errors.name[0] + '</li>';
                        }
         
        let errorHtml = '<div class="alert alert-danger" role="alert">' +
            '<b>Validation Error!</b>' +
            '<ul>' + nameValidation + descriptionValidation + '</ul>' +
        '</div>';
        $("#error-div").html(errorHtml);        
    }
                }
            });
        }
     
     
        /*
            edit record function
            it will get the existing value and show the project form
        */
        function editProject(id)
        {
            let url = $('meta[name=app-url]').attr("content") + "/projects/" + id ;
            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    let project = response.project;
                    $("#alert-div").html("");
                    $("#error-div").html("");   
                    $("#update_id").val(project.id);
                    $("#name").val(project.name);
                    $("#description").val(project.description);
                    $("#form-modal").modal('show'); 
                },
                error: function(response) {
                    console.log(response.responseJSON)
                }
            });
        }
     
        /*
            sumbit the form and will update a record
        */
        function updateProject()
        {
            $("#save-project-btn").prop('disabled', true);
            let url = $('meta[name=app-url]').attr("content") + "/projects/" + $("#update_id").val();
            let data = {
                id: $("#update_id").val(),
                name: $("#name").val(),
                description: $("#description").val(),
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "PUT",
                data: data,
                success: function(response) {
                    $("#save-project-btn").prop('disabled', false);
                    let successHtml = '<div class="alert alert-success" role="alert"><b>Project Updated Successfully</b></div>';
                    $("#alert-div").html(successHtml);
                    $("#name").val("");
                    $("#description").val("");
                    showAllProjects();
                    $("#form-modal").modal('hide');
                },
                error: function(response) {
                    /*
        show validation error
                    */
                    $("#save-project-btn").prop('disabled', false);
                    if (typeof response.responseJSON.errors !== 'undefined') 
                    {
                        console.log(response)
        let errors = response.responseJSON.errors;
        let descriptionValidation = "";
        if (typeof errors.description !== 'undefined') 
                        {
                            descriptionValidation = '<li>' + errors.description[0] + '</li>';
                        }
                        let nameValidation = "";
        if (typeof errors.name !== 'undefined') 
                        {
                            nameValidation = '<li>' + errors.name[0] + '</li>';
                        }
         
        let errorHtml = '<div class="alert alert-danger" role="alert">' +
            '<b>Validation Error!</b>' +
            '<ul>' + nameValidation + descriptionValidation + '</ul>' +
        '</div>';
        $("#error-div").html(errorHtml);        
    }
                }
            });
        }
     
        /*
            get and display the record info on modal
        */
        function showProject(id)
        {
            $("#name-info").html("");
            $("#description-info").html("");
            let url = $('meta[name=app-url]').attr("content") + "/projects/" + id +"";
            $.ajax({
                url: url,
                type: "GET",
                success: function(response) {
                    let project = response.project;
                    $("#name-info").html(project.name);
    $("#description-info").html(project.description);
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
        function destroyProject(id)
        {
            let url = $('meta[name=app-url]').attr("content") + "/projects/" + id;
            let data = {
                name: $("#name").val(),
                description: $("#description").val(),
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: url,
                type: "DELETE",
                data: data,
                success: function(response) {
                    let successHtml = '<div class="alert alert-success" role="alert"><b>Project Deleted Successfully</b></div>';
                    $("#alert-div").html(successHtml);
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