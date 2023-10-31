async function loginAdmin(){
    const login = document.querySelector('#login_adm').value

    if(String(login).trim() === ""){
        alert("Informe o login!")
        return
    }

    const password = document.querySelector('#password_adm').value

    if(String(password).trim() === ""){
        alert("Informe a senha!")
        return
    }

    const data = {
        login: login,
        password: password
    }

    const url = "/admin/login"

    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }

    const response = await fetch(url, options)

    if(response.status === 200){
        window.open('/admin', '_self')
    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }
}

//Cadastro de usuário
async function cadastrarUsuarioAdmin(){

    const erro = validar_campos('cmp_ps_nome', 'cmp_user_login', 'cmp_user_senha');

    if(erro === 1){
        return
    }

    limpar_validacao();

    const ps_nome    = document.querySelector('#cmp_ps_nome').value
    const user_login = document.querySelector('#cmp_user_login').value
    const user_senha = document.querySelector('#cmp_user_senha').value
    const ps_contato = document.querySelector('#cmp_ps_contato').value
    const ps_email   = document.querySelector('#cmp_ps_email').value
    const user_admin = document.querySelector('#cmp_user_admin').checked ? 1 : 0

    const url = '/admin/users/create'

    const data = {
        ps_nome     : ps_nome,
        user_senha  : user_senha,
        ps_contato  : ps_contato,
        ps_email    : ps_email,
        user_admin  : user_admin,
        user_login  : user_login
    }

    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }

    const response = await fetch(url, options)

    if(response.status === 200){
        alert("Usuário cadastrado!")
        window.open('/admin/users', '_self')
    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }

}

async function atualizarUsuarioAdmin(user_codigo){

    const erro = validar_campos('cmp_ps_nome', 'cmp_user_login');

    if(erro === 1){
        return
    }

    limpar_validacao();

    const ps_nome    = document.querySelector('#cmp_ps_nome').value
    const user_login = document.querySelector('#cmp_user_login').value
    const ps_contato = document.querySelector('#cmp_ps_contato').value
    const ps_email   = document.querySelector('#cmp_ps_email').value
    const user_admin = document.querySelector('#cmp_user_admin').checked ? 1 : 0

    const url = '/admin/users/update'

    const data = {
        ps_nome     : ps_nome,
        ps_contato  : ps_contato,
        ps_email    : ps_email,
        user_admin  : user_admin,
        user_login  : user_login,
        user_codigo : user_codigo
    }

    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }

    const response = await fetch(url, options)

    if(response.status === 200){
        alert("Usuário Atualizado!")
        window.open('/admin/users', '_self')
    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }

}

async function cadastrarCategoria(){

    const erro = validar_campos('cmp_cat_descricao')

    if(erro === 1){
        return
    }

    limpar_validacao()

    const cat_descricao = document.querySelector('#cmp_cat_descricao').value

    const url = "/admin/categorias/create"

    const data = {
        cat_descricao : cat_descricao
    }

    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }

    const response = await fetch(url, options)

    if(response.status === 200){
        alert("Categoria cadastrada!")
        window.open('/admin/categorias', '_self')
    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }

}

async function atualizarCategoria(cat_codigo){

    const erro = validar_campos('cmp_cat_descricao');

    if(erro === 1){
        return
    }

    limpar_validacao();

    const cat_descricao = document.querySelector('#cmp_cat_descricao').value

    const url = `/admin/categorias/update`

    const data = {
        cat_codigo    : cat_codigo,
        cat_descricao : cat_descricao
    }

    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }

    const response = await fetch(url, options)

    if(response.status === 200){
        alert("Categoria atualizada!")
        window.open('/admin/categorias', '_self')
    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }

}