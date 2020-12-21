define([
    'jquery',
    'core/ajax',
    'core/notification'   
], function($, ajax, notification){
    return{
        typereport: function(){ 
            $(document).ready(function(){
                $('#typereport option[value=default]').prop('selected', true);
                $('#report').load("forms/mystandartreports.php").one();
                $('#typereport').change(function(){                     
                    var selectedtypereport = $('#typereport').val();            
                    if (selectedtypereport == "default") {  
                        ajax.call([{
                            methodname: 'local_bookreport_changetodefaultreporttype',
                            args: {},
                            done: $('#report').load("forms/mystandartreports.php"),
                            //fail: notification.exception
                        }]);
                    } else if (selectedtypereport == "presentation") { 
                        ajax.call([{
                            methodname: 'local_bookreport_changetopresentationreporttype',
                            args: {},
                            done: $('#report').load("forms/mypresentationreports.php"),
                            //fail: notification.exception
                        }]);
                    }
                });
            });
        }
    };
});  