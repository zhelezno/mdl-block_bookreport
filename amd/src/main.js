define([    
    'jquery',
    'block_bookreport/jszip',//нужен для кнопки 'excelHtml5'
        
    'block_bookreport/jquery-ui',//DatePicker   
   
    'block_bookreport/buttons.print',//button.print подключает dataTables.buttons  и jquery.dataTables
    'block_bookreport/buttons.html5',//button.html5 подключает dataTables.buttons  и jquery.dataTables
    'block_bookreport/buttons.bootstrap4', //button.html5 подключает jquery.dataTables
], function($, jszip){
    return{
        dtInit: function(){
            function fetch(start_date, end_date) {               
                $.ajax({
                    url: "ajaxquery.php",
                    type: "POST",
                    data: {
                        start_date: start_date,
                        end_date: end_date
                    },
                    dataType: "json",
                    success: function(data) {            
                        //console.log(data);
                        window.JSZip = jszip;
                        // Datatables            
                        $('#reporttable').DataTable({
                            "data": data,
                            // buttons
                            "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                                "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                            "buttons": [
                                'excelHtml5', 'copyHtml5', 'print'
                            ],
                            // responsive
                            "responsive": true,
                            "columns": [
                                {
                                    "data": "author",
                                    "render": function(data, type, row, meta) {
                                        return '<a href="../bookreport/userreport.php?id='+row.id+'&userid='+row.user_id+'">'+row.author+'</a>';
                                    }           
                                },
                                {
                                    "data": "book",
                                    "render": function(data, type, row, meta) {
                                        return '<a href="../bookreport/userreport.php?id='+row.id+'&userid='+row.user_id+'">'+row.book+'</a>';
                                    }                 
                                },
                                {
                                    "data": "fullname",
                                    "render": function(data, type, row, meta) {
                                        return '<a href="../bookreport/userreport.php?id='+row.id+'&userid='+row.user_id+'">'+row.fullname+'</a>';
                                    }          
                                }, 
                                {
                                    "data": "department"                
                                },
                                {
                                    "data": "timecreated"           
                                },              
                                {
                                    "data": "type",
                                    "render": function(data, type, row, meta) {
                                        if (row.type = 1) {
                                            return '<img style="margin-right: 10px;" width="30px" src="../bookreport/style/img/reportpix1.png">';
                                        } else {
                                            return '<img style="margin-right: 10px;" width="30px" src="../bookreport/style/img/reportpix2.png">';
                                        }
                                    }
                                },                   
                            ]
                        });
                    }
                });
            }
            fetch();
            // Filter
            $(document).on("click", "#filter", function(e) {
                e.preventDefault();
                var start_date = $("#start_date").val();
                var end_date = $("#end_date").val();    
                if (start_date == "" || end_date == "") {
                    alert("both date required");
                } else {
                    $('#reporttable').DataTable().destroy();                    
                    start_date = new Date(start_date).getTime() / 1000;
                    end_date = new Date(end_date).getTime() / 1000;
                    //console.log(start_date+' '+end_date);               
                    fetch(start_date, end_date);
                }
            });
            // Reset
            $(document).on("click", "#reset", function(e) {
                e.preventDefault();
                $("#start_date").val(''); // empty value
                $("#end_date").val('');
                $('#reporttable').DataTable().destroy();
                fetch();
            });
           
        },
        dpInit: function(){
            $("#start_date").datepicker({
                "dateFormat": "yy-mm-dd"
            });
            $("#end_date").datepicker({
                "dateFormat": "yy-mm-dd"
            }); 
        }
    };
});  