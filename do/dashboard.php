<?php
/**
 * This dashboard file is designed to be a simple landing page for adjusting
 * the settings of components of the Manetheren Server automations network
 * It provides simple administrative links in order to run admin tasks
 */
?><!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="res/bootstrap.min.css">

    <style>
        #stickyhead {
            position:sticky;
            top:0;
            background: white;
            z-index: 99;
        }
    </style>

    <title>Hello, world!</title>
  </head>
  <body>
    <div class="container">
        <div class="row row-cols-1" id="stickyhead">
            <div class="col d-flex justify-content-center">
                <h1><center>Manage your Manetheren Services</center></h1>
            </div>
            <div class="col d-flex justify-content-center" id="userupdate">
                <div class="alert alert-secondary" role="alert">
                    Awaiting user input
                </div>
            </div>
        </div>

        <div class="row row-cols-2">
            <div class="col d-flex justify-content-center">
                <button type="button" class="btn btn-dark btn-block" href="lights-out.php">Lights out</button>
            </div>
            <div class="col d-flex justify-content-center">
                <button type="button" class="btn btn-light btn-block" href="lights-up.php">Lights up</button>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col d-flex justify-content-center">
                <h2><center>Manage Mirror</center></h2>
            </div>
        </div>
        <div class="row row-cols-2">
            <div class="col d-flex justify-content-center">
                <button type="button" class="btn btn-dark btn-block" href="serial-dim.php">Dim</button>
            </div>
            <div class="col d-flex justify-content-center">
                <button type="button" class="btn btn-light btn-block" href="serial-wake.php">Wake</button>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col d-flex justify-content-center">
                <h2><center>Manage bedside display</center></h2>
            </div>
        </div>
        <div class="row">
            <div class="col d-flex justify-content-center">
                <button type="button" class="btn btn-primary btn-block" href="tworivers-toggle.php">Toggle brightness</button>
            </div>
        </div>
        <br />
        <div class="row">
            <div class="col d-flex justify-content-center">
                <button type="button" class="btn btn-secondary btn-block" href="tworivers-set.php?level=10">10</button>
            </div>
            <div class="col d-flex justify-content-center">
                <button type="button" class="btn btn-secondary btn-block" href="tworivers-set.php?level=55">55</button>
            </div>
            <div class="col d-flex justify-content-center">
                <button type="button" class="btn btn-secondary btn-block" href="tworivers-set.php?level=155">155</button>
            </div>
        </div>
        <br />
        <div class="row row-cols-2">
            <div class="col d-flex justify-content-center">
                <button type="button" class="btn btn-dark btn-block" href="tworivers-set.php?level=0">Dim</button>
            </div>
            <div class="col d-flex justify-content-center">
                <button type="button" class="btn btn-light btn-block" href="tworivers-set.php?level=255">Wake</button>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="res/jquery.min.js"></script>
    <script src="res/bootstrap.min.js"></script>
    <script>
        $(()=>{
            $("button").on('click',function(e){
                e.preventDefault()
                var href = $(this).attr('href')

                $.get(href,(data)=>{

                    var alert = ''

                    if(!data || data===''){
                        var alert = $("<div>").attr('class','alert alert-warning').attr('role','alert').html('Action failed.')
                    } else {
                        var alert = $("<div>").attr('class','alert alert-success').attr('role','alert').html(data)
                    }
                    alert.hide()
                    $("#userupdate").empty().append( alert )
                    alert.fadeIn()
                })
            })
        })
    </script>
  </body>
</html>