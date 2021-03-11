define([    
    'jquery',
    'block_bookreport/jszip',//нужен для кнопки 'excelHtml5'
        
    'block_bookreport/jquery-ui',//DatePicker   
   
    'block_bookreport/buttons.print',//button.print подключает dataTables.buttons  и jquery.dataTables
    'block_bookreport/buttons.html5',//button.html5 подключает dataTables.buttons  и jquery.dataTables
    'block_bookreport/buttons.bootstrap4', //button.html5 подключает jquery.dataTables
], function($, jszip){
    return{
        dtInit: function(allreports, userid){ 
            //console.log(allreports);  
            //console.log(userid); 
            function fetch(allreports, userid, start_date = 0, end_date = 0) {  
                //console.log(allreports);  
                //console.log(userid); 
                //console.log(start_date); 
                //console.log(end_date);            
                $.ajax({
                    url: "ajaxselect.php",
                    type: "POST",
                    data: {                        
                        start_date: start_date,
                        end_date: end_date,
                        userid: userid
                    },
                    dataType: "json",
                    success: function(data) {            
                        //console.log(data);
                        window.JSZip = jszip;
                        // Datatables            
                        var table = $('#reporttable').DataTable({
                            "data": data,
                            // buttons
                            "dom": "<'row'<'col-sm-12 col-md-4'l><'col-sm-12 col-md-4'B><'col-sm-12 col-md-4'f>>" +
                                "<'row'<'col-sm-12'tr>>" +
                                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                            "buttons": [
                                'excelHtml5', 'copyHtml5', 'print'
                            ],
                            // russian localization
                            "language": {
                                            "processing": "Подождите...",
                                            "search": "Поиск:",
                                            "lengthMenu": "Показать _MENU_ записей",
                                            "info": "Записи с _START_ до _END_ из _TOTAL_ записей",
                                            "infoEmpty": "Записи с 0 до 0 из 0 записей",
                                            "infoFiltered": "(отфильтровано из _MAX_ записей)",
                                            "loadingRecords": "Загрузка записей...",
                                            "zeroRecords": "Записи отсутствуют.",
                                            "emptyTable": "В таблице отсутствуют данные",
                                            "paginate": {
                                                "first": "Первая",
                                                "previous": "Предыдущая",
                                                "next": "Следующая",
                                                "last": "Последняя"
                                            },
                                            "aria": {
                                                "sortAscending": ": активировать для сортировки столбца по возрастанию",
                                                "sortDescending": ": активировать для сортировки столбца по убыванию"
                                            },
                                            "select": {
                                                "rows": {
                                                    "_": "Выбрано записей: %d",
                                                    "0": "Кликните по записи для выбора",
                                                    "1": "Выбрана одна запись"
                                                },
                                                "1": "%d ряд выбран",
                                                "_": "%d ряда(-ов) выбрано",
                                                "cells": {
                                                    "1": "1 ячейка выбрана",
                                                    "_": "Выбрано %d ячеек"
                                                },
                                                "columns": {
                                                    "1": "1 столбец выбран",
                                                    "_": "%d столбцов выбрано"
                                                }
                                            },
                                            "searchBuilder": {
                                                "conditions": {
                                                    "string": {
                                                        "notEmpty": "Не пусто",
                                                        "startsWith": "Начинается с",
                                                        "contains": "Содержит",
                                                        "empty": "Пусто",
                                                        "endsWith": "Заканчивается на",
                                                        "equals": "Равно",
                                                        "not": "Не"
                                                    },
                                                    "date": {
                                                        "after": "После",
                                                        "before": "До",
                                                        "between": "Между",
                                                        "empty": "Пусто",
                                                        "equals": "Равно",
                                                        "not": "Не",
                                                        "notBetween": "Не между",
                                                        "notEmpty": "Не пусто"
                                                    },
                                                    "number": {
                                                        "between": "В промежутке от",
                                                        "empty": "Пусто",
                                                        "equals": "Равно",
                                                        "gt": "Больше чем",
                                                        "gte": "Больше, чем равно",
                                                        "lt": "Меньше чем",
                                                        "lte": "Меньше, чем равно",
                                                        "not": "Не",
                                                        "notBetween": "Не в промежутке от",
                                                        "notEmpty": "Не пусто"
                                                    }
                                                },
                                                "data": "Данные",
                                                "deleteTitle": "Удалить условие фильтрации",
                                                "logicAnd": "И",
                                                "logicOr": "Или",
                                                "title": {
                                                    "0": "Конструктор поиска",
                                                    "_": "Конструктор поиска (%d)"
                                                },
                                                "value": "Значение",
                                                "add": "Добавить условие",
                                                "button": {
                                                    "0": "Конструктор поиска",
                                                    "_": "Конструктор поиска (%d)"
                                                },
                                                "clearAll": "Очистить всё",
                                                "condition": "Условие"
                                            },
                                            "searchPanes": {
                                                "clearMessage": "Очистить всё",
                                                "collapse": {
                                                    "0": "Панели поиска",
                                                    "_": "Панели поиска (%d)"
                                                },
                                                "count": "{total}",
                                                "countFiltered": "{shown} ({total})",
                                                "emptyPanes": "Нет панелей поиска",
                                                "loadMessage": "Загрузка панелей поиска",
                                                "title": "Фильтры активны - %d"
                                            },
                                            "thousands": ",",
                                            "buttons": {
                                                "pageLength": {
                                                    "_": "Показать 10 строк",
                                                    "-1": "Показать все ряды",
                                                    "1": "Показать 1 ряд"
                                                },
                                                "pdf": "PDF",
                                                "print": "Печать",
                                                "collection": "Коллекция <span class=\"ui-button-icon-primary ui-icon ui-icon-triangle-1-s\"><\/span>",
                                                "colvis": "Видимость столбцов",
                                                "colvisRestore": "Восстановить видимость",
                                                "copy": "Копировать",
                                                "copyKeys": "Нажмите ctrl or u2318 + C, чтобы скопировать данные таблицы в буфер обмена.  Для отмены, щелкните по сообщению или нажмите escape.",
                                                "copySuccess": {
                                                    "1": "Скопирована 1 ряд в буфер обмена",
                                                    "_": "Скопировано %ds рядов в буфер обмена"
                                                },
                                                "copyTitle": "Скопировать в буфер обмена",
                                                "csv": "CSV",
                                                "excel": "Excel"
                                            },
                                            "decimal": ".",
                                            "infoThousands": ",",
                                            "autoFill": {
                                                "cancel": "Отменить",
                                                "fill": "Заполнить все ячейки <i>%d<i><\/i><\/i>",
                                                "fillHorizontal": "Заполнить ячейки по горизонтали",
                                                "fillVertical": "Заполнить ячейки по вертикали",
                                                "info": "Пример автозаполнения"
                                            }
                            },
                            // responsive
                            "responsive": true,
                            "columns": [
                                {
                                    "data": "author",
                                    "render": function(data, type, row, meta) {
                                        if (row.type == 1) {
                                            return '<a href="../bookreport/viewreport.php?id='+row.id+'&userid='+row.user_id+'">'+row.author+'</a>';
                                        } else {
                                            return '<a href="../bookreport/viewreport_pres.php?id='+row.id+'&userid='+row.user_id+'">'+row.author+'</a>';
                                        }
                                    }           
                                },
                                {
                                    "data": "book",
                                    "render": function(data, type, row, meta) {
                                        if (row.type == 1) {
                                            return '<a href="../bookreport/viewreport.php?id='+row.id+'&userid='+row.user_id+'">'+row.book+'</a>';
                                        } else {
                                            return '<a href="../bookreport/viewreport_pres.php?id='+row.id+'&userid='+row.user_id+'">'+row.book+'</a>';
                                        }
                                    }                 
                                },
                                {  
                                    "data": "fullname",
                                    "render": function(data, type, row, meta) {
                                        if (row.type == 1) {
                                            return '<a href="../bookreport/viewreport.php?id='+row.id+'&userid='+row.user_id+'">'+row.fullname+'</a>';
                                        } else {
                                            return '<a href="../bookreport/viewreport_pres.php?id='+row.id+'&userid='+row.user_id+'">'+row.fullname+'</a>';
                                        }
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
                                        if (row.type == 1) {
                                            return '<img style="margin-right: 10px;" width="30px" src="../bookreport/style/img/reportpix1.png">';
                                        } else {
                                            return '<img style="margin-right: 10px;" width="30px" src="../bookreport/style/img/reportpix2.png">';
                                        }
                                    }
                                },                   
                            ]                            
                        });
                        table.column( 2 ).visible( allreports );
                        table.column( 3 ).visible( allreports );
                    }
                });
            }
            fetch(allreports, userid);


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
                    fetch(allreports, userid, start_date, end_date);
                }
            });
            // Reset
            $(document).on("click", "#reset", function(e) {
                e.preventDefault();
                $("#start_date").val(''); // empty value
                $("#end_date").val('');
                $('#reporttable').DataTable().destroy();
                fetch(allreports, userid);
            });           
        },
        dpInit: function(){         
                
            //datepicker.setDefaults( datepicker.regional.ru );

            $("#start_date").datepicker({
                dateFormat: 'yy-mm-dd',
                closeText: 'Закрыть',
                prevText: '',
                currentText: 'Сегодня',
                monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
                    'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
                monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
                    'Июл','Авг','Сен','Окт','Ноя','Дек'],
                dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
                dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
                dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
                weekHeader: 'Не',                
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            });
            $("#end_date").datepicker({
                dateFormat: 'yy-mm-dd',
                closeText: 'Закрыть',
                prevText: '',
                currentText: 'Сегодня',
                monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
                    'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
                monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
                    'Июл','Авг','Сен','Окт','Ноя','Дек'],
                dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
                dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
                dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
                weekHeader: 'Не',                
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            });
            $("#calendariconstart").on("click", function() {
                $("#start_date").focus();
            });
            $("#calendariconend").on("click", function() {
                $("#end_date").focus();
            });
        }
    };
});  