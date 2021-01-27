define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/templates'   
], function($, ajax, notification, templates){
    return{
        typereport: function(author, book, mainactors, mainidea, quotes, conclusion){ 
            $(document).ready(function(){
                
                $('#typereport option[value=default]').prop('selected', true);
                
                element = $('#report');
                
                templates.render('block_bookreport/standartreport', {author, book, mainactors, mainidea, quotes, conclusion})
                .then(function(html, js) {                    
                    templates.replaceNodeContents(element, html, js);
                });
            
                $('#typereport').change(function(){ 
                    
                    var selectedtypereport = $('#typereport').val(); 
                    
                    if (selectedtypereport == "default") {  
                        
                        ajax.call([{
                            methodname: 'block_bookreport_changetodefaultreporttype',
                            args: {},
                            done:   templates.render('block_bookreport/standartreport', {author, book, mainactors, mainidea, quotes, conclusion})
                                    .then(function(html, js) {                    
                                       templates.replaceNodeContents(element, html, js);
                                    }),
                            //fail: notification.exception
                        }]);
                    } else if (selectedtypereport == "presentation") { 
                        
                        ajax.call([{
                            methodname: 'block_bookreport_changetopresentationreporttype',
                            args: {},
                            done:   templates.render('block_bookreport/presentationreport', {})
                                        .then(function(html, js) {                    
                                        templates.replaceNodeContents(element, html, js);
                                    }),
                            //fail: notification.exception
                        }]);
                    }
                });
            });
        },
        ajax_call_db: function(){

            function senddata(){            
                var author = $('#defaulttype_author').val();
                var book = $('#defaulttype_book').val();
                var mainactors = $('#defaulttype_mainactors').val();
                var mainidea = $('#defaulttype_mainidea').val();
                var quotes = $('#defaulttype_quotes').val();
                var conclusion = $('#defaulttype_conclusion').val();
                
                $.ajax({                
                    url: "ajaxinsert.php",
                    type: "POST",
                    data: {
                        author: author,
                        book: book,
                        mainactors: mainactors,
                        mainidea: mainidea,
                        quotes: quotes,
                        conclusion: conclusion                    
                    },
                    dataType: "json",  
                    complete: function(data){
                        
                        $("#autosavesuccess").show('slow');
                        setTimeout(function() { $("#autosavesuccess").hide('slow'); }, 2000);

                        $('#report').on('input', function() {  
                            setTimeout(senddata,5000);                            
                        });
                    }            
                });
            }
            $('#report').on('input', function() {      
                setTimeout(senddata,5000);
            });          
        },

    };
});  