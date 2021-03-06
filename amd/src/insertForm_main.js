define([
    'jquery',
    'core/ajax',
    'core/notification',
    'core/templates'   
], function($, ajax, notification, templates){
    return{
        ajax_call_db: function(){
            $('#report').change(function(){                                                        
                setTimeout(senddata,2000);
                setTimeout(show_autosave_success, 2000);                                
            });
            $('#button_clear').click(function(){
                $('.report-field').val('');
                senddata();
            });
            function show_autosave_success(){
                $("#autosavesuccess").show('slow');
                setTimeout(function(){
                    $("#autosavesuccess").hide('slow');
                }, 10000); 
            }

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
                    dataType: "text"
                });
            }    
        },
        ajax_call_booksearch_st: function(){            
            $('#defaulttype_book').keyup(function(){ 
                let booksearch = $("#defaulttype_book").val();                                                                      
                $.ajax({                
                    url: "ajaxsearch.php",
                    type: "POST",
                    data: {                           
                        booksearch: booksearch                                                
                    },
                    dataType: "html"
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
        },
        ajax_call_booksearch_pr: function(){       
            
            $("#id_book").after("<div class=\"col-md-12\" style=\"position: relative; margin-top: 0px; margin-left: 0px;\"><div class=\"list-group\" id=\"show-list\"></div></div>");      
            
            $('#id_book').keyup(function(){                 
                let booksearch = $("#id_book").val();                                                                   
                $.ajax({                
                    url: "ajaxsearch.php",
                    type: "POST",
                    data: {                           
                        booksearch: booksearch                                                
                    },
                    dataType: "html"                    
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
                
                $("#id_book").val(book);
                $("#id_author").val(author);
                $("#show-list").html("");
                
            });     
        }    
    };
});  