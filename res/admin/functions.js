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

    if(response.ok){
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

    if(response.ok){
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

    if(response.ok){
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

    if(response.ok){
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

    if(response.ok){
        alert("Categoria atualizada!")
        window.open('/admin/categorias', '_self')
    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }

}


//Início da seção para funções para produtos

let prd_categorias = [];

const fileInput = document.getElementById('hd_prd_imagens');
const imageDiv = document.getElementById('div_prd_imagens');

fileInput.addEventListener('change', function () {
    imageDiv.innerHTML = ''
    const formatosPermitidos = ['jpg', 'png', 'jpeg']

    for (const file of fileInput.files) {

        if(!formatosPermitidos.includes(file.name.split('.').pop())){
            alert("Apenas imagens são permitidas!");
            imageDiv.innerHTML = ''
            fileInput.value = ""
            break
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            const imageCard = document.createElement('div');
            imageCard.classList.add('prd-image-card', 'col-sm-6', 'col-md-4', 'form-group')

            const imagePreview = document.createElement('img')
            imagePreview.classList.add('prd-image-preview')
            imagePreview.src = e.target.result

            imageCard.appendChild(imagePreview)
            imageDiv.appendChild(imageCard)
        }

        reader.readAsDataURL(file)
    }

});

function preencherArrayCategorias(){
    const chk_categorias = document.querySelectorAll('.chk-categorias')
    chk_categorias.checked = false

    chk_categorias.forEach(function(chk) {
        if(chk.checked){
            prd_categorias.push(chk.dataset.catCodigo)
        }
        else if(prd_categorias.includes(chk.dataset.catCodigo)){
            var indice = prd_categorias.indexOf(chk.dataset.catCodigo)
            prd_categorias.splice(indice, 1)
        }
    })
}

async function cadastrarProduto(){
    const erro = validar_campos('cmp_prd_descricao', 'cmp_prd_preco', 'cmp_prd_peso', 'cmp_prd_largura', 'cmp_prd_altura', 'cmp_prd_comprimento', 'cmp_prd_obs');

    if(erro === 1){
        return
    }

    if(prd_categorias.length < 1){
        alert("Selecione ao menos uma categoria")
        return
    }

    if(fileInput.files.length < 1){
        alert("Insira ao menos uma imagem")
        return
    }

    debugger
    const formData = new FormData();

    formData.append('prd_descricao', document.querySelector('#cmp_prd_descricao').value)
    formData.append('prd_preco', document.querySelector('#cmp_prd_preco').value)
    formData.append('prd_peso', document.querySelector('#cmp_prd_peso').value)
    formData.append('prd_largura', document.querySelector('#cmp_prd_largura').value)
    formData.append('prd_altura', document.querySelector('#cmp_prd_altura').value)
    formData.append('prd_comprimento', document.querySelector('#cmp_prd_comprimento').value)
    formData.append('prd_obs', document.querySelector('#cmp_prd_obs').value)

    const categorias = document.querySelectorAll('.chk-categorias:checked')
    categorias.forEach(chk => {
        formData.append('categorias[]', chk.dataset.catCodigo)
    })

    for(const file of fileInput.files){
        formData.append('imagens[]', file)
    }

    const url = "/admin/products/create"

    const options = {
        method: "POST",
        body: formData
    }

    const response = await fetch(url, options)

    if(response.ok){
        alert("Produto cadastrado!")
        window.open('/admin/products', '_self')
    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }

}

//Fim da seção de funções para produtos