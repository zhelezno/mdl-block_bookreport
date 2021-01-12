define([
    'jquery',
    'block_bookreport/datatables'  
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