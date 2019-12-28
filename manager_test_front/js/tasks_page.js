document.write("<script type='text/javascript' src='./js/main.js?v=22'></script>");
function tasksPage(handleData) {
    tasksTable(function (data) {
        //get users
        $.ajax({
            type: 'GET',
            url: '../manager_test_back/user',
            data: {limit : 99999},
            success:function(users){
                users = JSON.parse(users);
                let select = '<select id="user_select" style="margin-bottom: 10px">';
                users.forEach(function (user) {
                    select+='<option value="'+user['id']+'">'+user['name']+'</option>';
                });
                select+='</select><br>';
                let form = '' +
                    '<h5>Create Task</h5>'+
                    select+
                    '<input class="form-control" type="text" id="title" placeholder="title" style="margin-bottom: 10px">'+
                    '<textarea class="form-control" id="description" placeholder="description" rows="4" style="margin-bottom: 10px"></textarea>'+
                    '<button class="btn btn-outline-primary" onclick="createTask()">Create</button>'+
                    '';
                handleData(data+form);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                alert(textStatus+errorThrown);
            }
        });
    });
}

function tasksTable(handleData){
    let attrs = [];
    attrs.push({text: 'ID', val: 'id'});
    attrs.push({text: 'Title', val: 'title'});
    attrs.push({text: 'Description', val: 'description'});
    attrs.push({text: 'Author', val: 'user_name'});
    attrs.push({text: 'Creation date', val: 'creation_date'});

    $.ajax({
        type: 'GET',
        url: '../manager_test_back/task',
        data: {user_id : localStorage.getItem('user_id')},
        success : function(result)
        {
            result = JSON.parse(result);
            let table = '<table class="table">';
            table += '<thead><tr>';
            attrs.forEach(attr => table += '<th>'+attr.text+'</th>');
            table+='<th></th>';

            table += '</tr></thead>';
            result.forEach(function(element){
                table+= '<tr>';
                attrs.forEach(attr => table += '<td>'+element[attr.val]+'</td>');
                table+='<td><button class="btn btn-outline-danger" onclick="deleteTask('+element["id"]+')">Delete</button></td>';
                table+='</tr>';
            });
            table+='</table>';
            handleData(table);

        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(textStatus+errorThrown);
        }
    });
}

function createTask(user_id=null){
    if($("#title").val()==''){
        alert('Title is required');
        return;
    }
    if(user_id===null){
        //get from the select form
        user_id=$("#user_select").val();
    }
    $.ajax({
        method: "POST",
        url: "../manager_test_back/task",
        data: { title: $("#title").val(), description: $("#description").val(), user_id: user_id}
    }).done(function() {
        reloadPage('tasks');
    });
}

function deleteTask(id){
    $.ajax({
        method: "DELETE",
        url: "../manager_test_back/task/"+id,
    }).done(function() {
        let user_id = localStorage.getItem('user_id');
        if(user_id!=null){
            reloadPage('user');
        }else{
            reloadPage('tasks');
        }
    });
}