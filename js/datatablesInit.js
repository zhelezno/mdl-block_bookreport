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

            // Datatables            
            $('#reporttable').DataTable({
                "data": data,
                // buttons
                "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "buttons": [
                    'excel', 'csv', 'print'
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
                                return '<img style="margin-right: 10px;" width="30px" src="../bookreport/pix/reportpix1.png">';
                            } else {
                                return '<img style="margin-right: 10px;" width="30px" src="../bookreport/pix/reportpix2.png">';
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
