$(document).ready(function() {
    $("#dnd-table-var").tableDnD({
	    onDragClass: "myDragClass",
	    onDrop: function(table, row) {
          var rows = table.tBodies[0].rows;
          var sortord = "";
          for (var i = 0; i < rows.length; i++) {
            sortord += rows[i].id + ";";
          }

          $.ajax({
        		type: "POST",
         		url: "/admin/products?reorder-vars",
         		timeout: 5000,
         		data: "sortord=" + sortord
         	});
        }
  	});


});