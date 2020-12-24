define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/templates'   
], function($, ajax, notification, templates){
    return{
        typereport: function(){ 
            $(document).ready(function(){
                
                

                $('#typereport option[value=default]').prop('selected', true);
                
                element = $('#report');
                
                templates.render('block_bookreport/standartreport', {})
                .then(function(html, js) {                    
                    templates.replaceNodeContents(element, html, js);
                });

                //$('#report').load("views/standartreport.php").one();
                $('#typereport').change(function(){ 
                    
                    var selectedtypereport = $('#typereport').val(); 
                    
                    if (selectedtypereport == "default") {  
                        
                        ajax.call([{
                            methodname: 'block_bookreport_changetodefaultreporttype',
                            args: {},
                            done:   templates.render('block_bookreport/mystandartreport', {})
                                    .then(function(html, js) {                    
                                       templates.replaceNodeContents(element, html, js);
                                    }),
                            //fail: notification.exception
                        }]);
                    } else if (selectedtypereport == "presentation") { 
                        
                        ajax.call([{
                            methodname: 'block_bookreport_changetopresentationreporttype',
                            args: {},
                            done:   templates.render('block_bookreport/mypresentationreport', {})
                                        .then(function(html, js) {                    
                                        templates.replaceNodeContents(element, html, js);
                                    }),
                            //fail: notification.exception
                        }]);
                    }
                });
            });
        }
    };
});  