document.write("<script type='text/javascript' src='./js/main.js?v=22'></script>");
function userPage(handleData) {
    let id = localStorage.getItem('user_id');

    $.ajax({
        type: 'GET',
        url: '../manager_test_back/user/'+id,
        success : function(result)
        {
            result = JSON.parse(result);
            // console.log(result);
            let html='<h5>ID: '+result['id']+'</h5>';
            html+='<h5>Name: '+result['name']+'</h5>';
            html+='<h5>Email: '+result['email']+'</h5>';
            html+='</br>';
            html+='<h3>Related Tasks</h3>';

            let form = '' +
                '<h5>Create Task</h5>'+
                '<input class="form-control" type="text" id="title" placeholder="title" style="margin-bottom: 10px">'+
                '<textarea class="form-control" id="description" placeholder="description" rows="4" style="margin-bottom: 10px"></textarea>'+
                '<button class="btn btn-outline-primary" onclick="createTask('+result["id"]+')">Create</button>'+
                '';

            tasksTable(function (data) {
                handleData(html+data+form);
            })
            /*tasksPage(function (data) {
                handleData(html+data);
            })*/
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(textStatus+errorThrown);
        }
    });
}