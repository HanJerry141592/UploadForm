<body>
<div class="jumbotron text-center">
    <h1>Upload Form</h1>
</div>

<div class="container">
    <div class="card">
        <div class="card-body">
            <div class="alert alert-success" role="alert" id="success_alarm" style="display: none">
                File upload finished successfully!
            </div>
            <div class="alert alert-warning" role="alert" id="error_alarm" style="display: none">
                Error!
            </div>
            <h4 class="card-title">Plants and logistics facilities and site characteristics </h4>
            <hr>
            <form action="/action_page.php" id="upload_form">

                <div class="form-group">
                    <label for="upload_file" class="mr-sm-4">Select File:</label>
                    <input type="file" class="form-control mb-2 mr-sm-8" id="upload_file" name="upload_file">
                </div>
                <div class="container row_information_panel" style="display: none;">
                    <div class="form-group form-inline">
                        <label for="upload_file" class="mr-sm-4">Total Rows:</label>
                        <label class="mr-sm-4 t_rows"></label>

                    </div>
                    <div class="form-group form-inline">
                        <label class="mr-sm-4 ">Start Row Number: </label>
                        <input type="number" class="form-control mb-2 mr-sm-8 start_num" min="1" name="start_num">

                        <label class="mr-sm-4 ml-5 ">End Row Number: </label>
                        <input type="number" class="form-control mb-2 mr-sm-8 end_num" min="1" id="upload_file" name="end_num">
                    </div>
                </div>

                <div class="form-group">
                    <label for="geocoding_selector">Select Geocoding Service</label>
                    <select class="form-control mb-2 mr-sm-5" name="geocoding_selector" id="geocoding_selector">
                        <option value="uni_heid">Uni Heidelberg openroute</option>
                        <option value="google_map">Google maps</option>
                        <option value="no_geocoding">No geocoding</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="email" class="mr-sm-4">Would you like to append or replace the data in the
                        table?</label>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="table_replace_selector_1"
                               name="table_replace_selector" value="append" checked>
                        <label class="custom-control-label" for="table_replace_selector_1">Append</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" class="custom-control-input" id="table_replace_selector_2"
                               name="table_replace_selector" value="replace">
                        <label class="custom-control-label" for="table_replace_selector_2">Replace</label>
                    </div>
                </div>

                <button class="btn btn-primary btn_load" type="button" disabled style="display: none">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    &nbsp;&nbsp;Loading...&nbsp;&nbsp;
                </button>

                <button class="btn btn-primary btn_import" type="button">
                    &nbsp;&nbsp;&nbsp;&nbsp;Import&nbsp;&nbsp;&nbsp;&nbsp;
                </button>
            </form>
        </div>
    </div>
    <div class="row p-lg-2">
        <div class="container">
            <a href="<?php echo base_url() . 'UploadManager/mapIndex' ?>">Manage MappingTable</a>
        </div>
    </div>
</div>


<!-- loader-->
<div class="modal fade bd-example-modal-lg" data-backdrop="static" data-keyboard="false" tabindex="-1"
     id="loader_modal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" style="width: 48px">
            <span class="fa fa-spinner fa-spin fa-3x"></span>
        </div>
    </div>
</div>


<!-- Dialog modal-->
<div class="modal fade" id="dialog_modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Modal Heading</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                Modal body..
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<!-- Api selector modal -->
<div class="modal fade" id="api_modal">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Api Selecting System</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                There is no API data for this table.
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submit_api">Submit</button>
            </div>

        </div>
    </div>
</div>

</body>
</html>
<script src="<?= base_url() ?>/assets/js/upload_globalfunction.js"></script>
<script>

    var api = [];
    var data;

    var array_data;
    var array_fields;
    function handleFileSelect(evt) {
        var file = evt.target.files[0];

        Papa.parse(file, {
            header: true,
            dynamicTyping: true,
            complete: function(results) {
                array_data = results.data;
                array_fields = results.meta.fields;
                $('.row_information_panel').css('display','block');
                $('.row_information_panel').find('.t_rows').html(parseInt(array_data.length)-1);
                $('.row_information_panel').find('.start_num').val(1);
                $('.row_information_panel').find('.end_num').val(parseInt(array_data.length)-1);
            }
        });
    }

    $(document).ready(function(){
        $("#upload_file").change(handleFileSelect);
    });

    $('.btn_import').on('click', function () {
        $('#error_alarm').css('display', 'none');

        hideSuccessAlart();
        formTag = $('#upload_form');
        var form = formTag[0];
        data = new FormData(form);
        var file_path = $('#upload_file').val();

        var startIndex = (file_path.indexOf('\\') >= 0 ? file_path.lastIndexOf('\\') : file_path.lastIndexOf('/'));
        var filename = file_path.substring(startIndex);
        if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
            filename = filename.substring(1);
        }


        var extension = getFileExtension(filename);

        if (extension === 'csv' || extension === 'xls' || extension === 'xlsx' || extension === 'json') {
            data.append('file_name', filename);
            getApiList()
        } else {
            disp_alert("Alert!", "Please select the correct file.");
        }
    });


    /* Get api list and Display in the POPup modal */
    function getApiList() {
        $.ajax({
            url: "<?php echo base_url()?>UploadManager/getApiList",
            type: "post",
            dataType: "json",
            data: {
                table: 'm_locations'
            },
            success: function (res) {
                content = '';
                if (res.state === 'success') {
                    api = res.data;
                    var content = '<form id="api_form">';
                    api.forEach(function (value) {
                        content += "<div class=\"custom-control custom-checkbox custom-control-inline\">\n" +
                            "<input type=\"checkbox\" class=\"custom-control-input\" id=\"" + value + "\" name=\"" + value + "\">\n" +
                            "<label class=\"custom-control-label\" for=\"" + value + "\">" + value + "</label>\n" +
                            "</div>"
                    })

                    content += "</form>";
                }
                $('#api_modal').find('.modal-body').html(content);
                $('#api_modal').modal('show');

            }
        })
    }

    $('#submit_api').on('click', function () {

        start_num = $('.start_num').val();
        end_num = $('.end_num').val();

        if (parseInt(start_num) > parseInt(end_num) || parseInt(start_num) < 1 || parseInt(end_num) > array_data.length-1 || array_data.length < 1){
            $('#error_alarm').css('display', 'block');
            $('#error_alarm').html('Please select correct start and end values.');
            return;
        }

        api_list = [];
        api_data = $('#api_form').serializeArray();
        api_data.forEach(function (val) {
            api_list.push(val.name);
        });
        data.append('api_list', api_list);
        data.append('array_fields', array_fields);
        $('#api_modal').modal('hide');

        loader();
        $.ajax({
            url: "<?php echo base_url(); ?>FileManager/upload_location",
            type: "post",
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            cache: false,
            data: data,
            dataType: "json",
            success: function (res) {
                exit_loader();
                if (res == 'success') {
                    dispSuccessAlarm("File uploaded successfully");
                }
            },
            error: function () {
                exit_loader();
            }
        })
    })
</script>
