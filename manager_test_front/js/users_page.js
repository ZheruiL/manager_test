document.write("<script type='text/javascript' src='./js/main.js?v=22'></script>");
function usersPage(handleData) {
    let attrs = [];
    attrs.push({text: 'ID', val: 'id'});
    attrs.push({text: 'Name', val: 'name'});
    attrs.push({text: 'Email', val: 'email'});

    $.ajax({
        type: 'GET',
        url: '../manager_test_back/user',
        success : function(result)
        {
            result = JSON.parse(result);
            let table = '<table class="table">';
            table += '<thead><tr>';
            attrs.forEach(attr => table += '<th>'+attr.text+'</th>')
            table+='<th></th>';

            table += '</tr></thead>';
            result.forEach(function(element){
                //console.log(element['id']);
                table+= '<tr>';
                attrs.forEach(function (attr){
                    table+='<td>';
                    if(attr.val === 'name'){
                        table += '<a href="javascript:void(0)" onclick="showUser('+element["id"]+')">'+element[attr.val]+'</a>'
                    }
                    else{
                        table += element[attr.val];
                    }
                    table+='</td>';
                });
                table+='<td><button class="btn btn-outline-danger" onclick="deleteUser('+element["id"]+')">Delete</button></td>';
                table+='</tr>';
            });
            let form = '' +
                '<tr>' +
                    '<td></td>'+
                    '<td><input class="form-control" type="text" id="name"></td>'+
                    '<td><input class="form-control" type="email" id="email"></td>'+
                    '<td><button class="btn btn-outline-primary" onclick="createUser()">Create</button></td>'+
                '</tr>';

            table+=form;
            table+='</table>';
            handleData(table);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert(textStatus+errorThrown);
        }
    });
}
function showUser(id){
    localStorage.setItem('user_id', id);
    reloadPage('user');
}
function deleteUser(id){
    $.ajax({
        method: "DELETE",
        url: "../manager_test_back/user/"+id,
    }).done(function() {
        reloadPage('users');
    });
}
function createUser(){
    if($("#name").val()==''){
        alert('name is required');
        return;
    }
    if($("#email").val()==''){
        alert('email is required');
        return;
    }
    $.ajax({
        method: "POST",
        url: "../manager_test_back/user",
        data: { name: $("#name").val(), email: $("#email").val() }
    }).done(function() {
        reloadPage('users');
    });
}