var EcommerceProducts = function () {

    var initPickers = function () {
        //init date pickers
        $('.date-picker').datepicker({
            rtl: Metronic.isRTL(),
            autoclose: true
        });
    }

    var handleProducts = function() {
        var grid = new Datatable();

        grid.init({
            src: $("#datatable_products"),
            onSuccess: function (grid) {
                // execute some code after table records loaded
            },
            onError: function (grid) {
                // execute some code on network or other general error  
            },
            loadingMessage: 'Loading...',
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options 
                "lengthMenu": [
                    [10, 20, 50, 100, 150],
                    [10, 20, 50, 100, 150] // change per page values here 
                ],
                "pageLength": 10, // default record count per page
                "ajax": {
                    //"url": base_url+"?r=admin/article/json", // ajax source
                    "url": get_url("admin/article/list"), // ajax source

                },
                "order": [
                    [1, "desc"],[2, "desc"]
                ], // set first column as a default sort by asc
                "columns": [
                    { "data": "check"},
                    { "data": "id" },
                    { "data": "title" },
                    { "data": "cates" },
                    { "data": "add_time" },
                    { "data": "summary" },
                    { "data": "available_from" },
                    { "data": "status" }
                ],
                "columnDefs": [//各列特殊处理
                    {
                        "targets": [0], // 目标列位置，下标从0开始
                        //"data": "check", // 数据列名 如果定义了 columns 属性可用  
                        "render": function(data, type, full) { // 返回自定义内容
                            return "<input type='checkbox' value='"+full.id+"'>";
                        }
                    },
                    {
                        "targets": [7], // 目标列位置，下标从0开始
                        //"data": "check", // 数据列名 如果定义了 columns 属性可用  
                        "render": function(data, type, full) { // 返回自定义内容
                            if(data==0){
                                return '<span class="label label-danger">未发布</span>';
                            }else{
                                return '<span class="label label-success">已发布</span>';
                            }
                        }
                    },
                    {
                      "targets": [8], // 目标列位置，下标从0开始
                      "data": "id", // 数据列名 传入数据的列
                      "render": function(data, type, full) { // 返回自定义内容
                            return "<a href='" + base_url+"?r=admin/article/delete&id=" +data+ "'>Delete</a>&nbsp;<a href='" + base_url+"?r=admin/article/edit&id="+ data + "'>Update</a>";
                      }
                  }
                ]
            }
        });

         // handle group actionsubmit button click
        grid.getTableWrapper().on('click', '.table-group-action-submit', function (e) {
            e.preventDefault();
            var action = $(".table-group-action-input", grid.getTableWrapper());
            if (action.val() != "" && grid.getSelectedRowsCount() > 0) {
                grid.setAjaxParam("customActionType", "group_action");
                grid.setAjaxParam("customActionName", action.val());
                grid.setAjaxParam("id", grid.getSelectedRows());
                grid.getDataTable().ajax.reload();
                grid.clearAjaxParams();
            } else if (action.val() == "") {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: '请选择一种操作',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            } else if (grid.getSelectedRowsCount() === 0) {
                Metronic.alert({
                    type: 'danger',
                    icon: 'warning',
                    message: 'No record selected',
                    container: grid.getTableWrapper(),
                    place: 'prepend'
                });
            }
        });
    }

    return {

        //main function to initiate the module
        init: function () {

            handleProducts();
            initPickers();
            
        }

    };

}();