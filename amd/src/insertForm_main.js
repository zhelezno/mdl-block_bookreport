define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/templates'   
], function($, ajax, notification, templates){
    return{
        typereport: function(){ 
            $(document).ready(function(){
            
                $('#typereport').change(function(){ 
                    
                    var selectedtypereport = $('#typereport').val(); 
                    
                    if (selectedtypereport == "default") {  

                        $(location).attr('href', 'index.php');
                    } else if (selectedtypereport == "presentation") {  

                        $(location).attr('href', 'filepickform.php');
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
                    url: "ajaxsendreport.php",
                    type: "POST",
                    data: {
                        author: author,
                        book: book,
                        mainactors: mainactors,
                        mainidea: mainidea,
                        quotes: quotes,
                        conclusion: conclusion                    
                    },
                    dataType: "text",
                    complete: function(data) {
                        //console.log(data);
                    }
                }).done(function(){
                    $("#autosavesuccess").show('slow');
                    setTimeout(function() { $("#autosavesuccess").hide('slow'); }, 10000);                            
                });
            }    
             
            $(document).ready(function(){
                $('#report').change(function(){                                                        
                    setTimeout(senddata,2000);                                    
                });
            });
        },
        ajax_call_booksearch: function(){            
            $('#defaulttype_book').keyup(function(){ 
                let booksearch = $("#defaulttype_book").val();                                                                      
                $.ajax({                
                    url: "ajaxsearch.php",
                    type: "POST",
                    data: {                           
                        booksearch: booksearch                                                
                    },
                    dataType: "html",
                    complete: function(data) {
                        console.log(data);
                    }
                }).done(function(data){
                    $("#show-list").html(data);                        
                }).fail(function(){
                    $("#show-list").html("");   
                });
            });
            $(document).on("click", "#searchresult", function (e) {
                let selectedbook = e.target.text;
                let strindex = selectedbook.indexOf('-');
                let book = selectedbook.substr(strindex+1).trim();
                let author = selectedbook.split('-')[0].trim();
                $("#defaulttype_book").val(book);
                $("#defaulttype_author").val(author);
                $("#show-list").html("");  
            });     
        }
    };
});  