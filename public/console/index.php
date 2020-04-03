<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="description" content="REST-API console">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <title>Akaash: API Testing Console</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="styles.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
        <script src="console.js?v=<?= time(); ?>"></script>
    </head>
    <body>
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark">
            <!-- Brand -->
            <a class="navbar-brand" href="#">Akaash</a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#">Documentation</a>
                </li>
            </ul>
        </nav>

        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-3">
                    <div id="apiExample">
                    </div> 
                </div>
                <div class="col-sm-8">
                    <div id="apiForm">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Method</label>
                            <div class="col-sm-10">
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="method" value="GET">GET
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="method" value="POST" checked>POST
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="method" value="PUT">PUT
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <label class="form-check-label">
                                        <input type="radio" class="form-check-input" name="method" value="DELETE">DELETE
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Base URL</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="baseUrl" placeholder="site url">
                                <small class="form-text text-muted">
                                    check base url before submitting API request
                                </small>
                            </div>
                            <label class="col-sm-2 col-form-label text-right">API Path</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="apiPath" placeholder="api endpoint here">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Query Params</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="queryParams" placeholder="e.g; param1=abc&param2=123">
                            </div>
                        </div>
<!--                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">User ID</label>
                            <div class="col-sm-3">
                                <input type="text" class="form-control" name="userId" readonly>
                            </div>
                        </div>-->
                        <div class="form-group row form-border">
                            <div class="container-fluid">
                                <div class="row">
                                    <form>

                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="button" class="btn btn-primary" id="submit" disabled onclick="callApi()">Submit</button>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Authorization Token</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" name="authorization" placeholder="put authorization token here"></textarea>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <ul class="nav nav-tabs">
                                    <li class="nav-item">
                                        <!--<a data-toggle="tab" href="#home">Home</a>-->
                                        <a class="nav-link active" data-toggle="tab" href="#jsonResponse">Response</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-toggle="tab" href="#serverHeaders">Header</a>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    <div id="jsonResponse" class="tab-pane fade active show">
                                        <div class="row">
                                            <div class="col">
                                                <pre id="jsonOutput"></pre>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="serverHeaders" class="tab-pane fade">
                                        <div class="row">
                                            <div class="col">
                                                <div id="responseHeader">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
    </body>
</html> 





















