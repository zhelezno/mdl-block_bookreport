define([
    'jquery',
    'block_bookreport/jquery.Datatables'  
], function($, datatables){
    return{
        datatablesinit: function(){ 
            $(document).ready(function(){                
				$('#reporttable').DataTable({				
				});
            });
        }
    };
});  