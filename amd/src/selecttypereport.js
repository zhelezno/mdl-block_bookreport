define([
    'jquery',
    'core/ajax',
    'core/notification'   
], function($, ajax, notification){
    return{
        typereport: function(){ 
            $(document).ready(function(){
                $('#typereport option[value=default]').prop('selected', true);
                $('#report').load("views/standartreport.php").one();
                $('#typereport').change(function(){                     
                    var selectedtypereport = $('#typereport').val();            
                    if (selectedtypereport == "default") {  
                        ajax.call([{
                            methodname: 'block_bookreport_changetodefaultreporttype',
                            args: {},
                            done: $('#report').load("views/standartreport.php"),
                            //fail: notification.exception
                        }]);
                    } else if (selectedtypereport == "presentation") { 
                        ajax.call([{
                            methodname: 'block_bookreport_changetopresentationreporttype',
                            args: {},
                            done: $('#report').load("views/presentationreport.php"),
                            //fail: notification.exception
                        }]);
                    }
                });
            });
        }
    };
});  