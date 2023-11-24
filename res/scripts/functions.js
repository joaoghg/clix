// Script formatacao CEP
function cep1(event) {
    let input = event.target;
    input.value = cep2(input.value);
}

function cep2(value) {
    if (!value) return "";
    value = value.replace(/\D/g, '');
    value = value.replace(/(\d{5})(\d)/, '$1-$2');
    return value;
}

// Script formatacao Telefone
function handlePhone(event) {
    let input = event.target
    input.value = phoneMask(input.value)
}

function phoneMask(value) {
    if (!value) return ""

    value = value.replace(/\D/g, '')
    value = value.replace(/(\d{2})(\d)/, "($1) $2")
    value = value.replace(/(\d)(\d{4})$/, "$1-$2")

    return value
}

// placeholder Select
function hidePlaceholder() {
    const select = document.getElementById("estados");
    select.options[0].style.display = "none";
}

//Formatando campos de decimais
function maskDecimal2(campo){

    let valor = campo.value.replace(/[^0-9,]/g, '')

    if(valor.trim() !== "" && valor.indexOf(',') != 0){

        valor = valor.replace(/,/g, '.')

        const parts = valor.split('.');
        if (parts.length > 2) {
            valor = parts[0] + '.' + parts[1];
        }

        if(valor.indexOf('.') !== -1 && parts[1].length > 2){
            campo.value = String(Number(valor).toFixed(2)).replace('.', ',')
        }
        else{
            campo.value = valor.replace('.', ',')
        }

    }
    else{
        campo.value = ''
    }

}

function validar_campos(...ids){
    let erro = 0

    ids.forEach(id => {
        let campo = document.querySelector(`#${id}`)

        if(campo.value.trim() === ""){
            erro = 1
            campo.classList.add('is-invalid')
        }
    })

    return erro
}

function limpar_validacao(...ids){

    if(ids){
        ids.forEach(id => {
            let campo = document.querySelector(`#${id}`)

            campo.classList.remove('is-invalid')
        })
    }
    else{
        let campos = document.querySelectorAll('input, textarea, select')

        campos.forEach(campo => {
            campo.classList.remove('is-invalid')
        })
    }

}

function abrirModal(id){
    
    if(id === "modal_prd_categorias"){
        const chk_categorias = document.querySelectorAll('.chk-categorias')

        chk_categorias.forEach(function(chk) {
            if(!prd_categorias.includes(chk.dataset.catCodigo)){
                chk.checked = false;
            }
            else{
                chk.checked = true;
            }
        })
    }

    $(`#${id}`).modal('show');

}

//Funções checkout

async function buscaCep(){

    const inputCep = document.querySelector('#cmp_checkout_cep')

    if(inputCep.value.replace(/\D/, '').length < 8 ){
        return
    }

    const cep = inputCep.value.replace(/\D/, '')

    const url = `https://viacep.com.br/ws/${cep}/json`

    const response = await fetch(url)

    if(!response.ok){
        return
    }

    const data = await response.json()

    document.querySelector('#cmp_checkout_rua').value = data.logradouro
    document.querySelector('#cmp_checkout_complemento').value = data.complemento
    document.querySelector('#cmp_checkout_bairro').value = data.bairro
    document.querySelector('#estados').value = data.uf
    document.querySelector('#cmp_checkout_cidade').value = data.localidade

}
//

//Funções carrinhos

function adicionar_carrinho(){
    window.open("/cart","_self")
}

//


async function filtrarProdutos(){

    const formData = new FormData()

    document.querySelectorAll('.chkCat:checked').forEach(function(chk){

        formData.append('categorias[]', chk.value)

    })

    formData.append('preco_min', document.querySelector('#price-min').value)
    formData.append('preco_max', document.querySelector('#price-max').value)

    const options = {
        method: "POST",
        body: formData
    }

    const url = "/store";

    const response = await fetch(url, options)

    if(response.ok){

        const data = await response.json()

        let html = ''
        data.produtos.forEach((prd) => {

            html += `
                <div class="col-md-4 col-xs-6">
                    <div class="product">
                        <div class="product-img">
                            <img src="${prd.img_caminho}" alt="">
                        </div>
                        <div class="product-body">
                            <p class="product-category">${prd.prd_categoria}</p>
                            <h3 class="product-name"><a href="/product/${prd.prd_codigo}">${prd.prd_descricao}</a></h3>
                            <h4 class="product-price">R$ ${prd.prd_preco.replace('.', ',')} </h4>
                            <div class="product-rating">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <div class="product-btns">
                                <button class="add-to-wishlist" onclick="addWishlist(${prd.prd_codigo})"><i class="fa fa-heart-o"></i><span class="tooltipp">adicionar aos favoritos</span></button>
                            </div>
                        </div>
                        <div class="add-to-cart">
                            <button class="add-to-cart-btn" onclick="addCart(${prd.prd_codigo})"><i class="fa fa-shopping-cart"></i> adicionar ao carrinho</button>
                        </div>
                    </div>
                </div>
            `

        })

        document.querySelector('#div_produtos_store').innerHTML = html

    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }

}

async function addWishlist(prd_codigo){

    let produtos = JSON.parse(localStorage.getItem('wishlist')) || {}

    produtos[prd_codigo] = prd_codigo

    localStorage.setItem('wishlist', JSON.stringify(produtos))

    countWishlist()

}

function countWishlist(){

    let produtos = JSON.parse(localStorage.getItem('wishlist')) || {}

    document.querySelector('#qtd_wishlist').innerHTML = Object.keys(produtos).length

}

async function carregarProdutosWishlist(){

    let produtos = JSON.parse(localStorage.getItem('wishlist')) || {}

    let html = ''
    let totalWishlist = 0
    for (const key of Object.keys(produtos)) {

        const response = await fetch(`/products/${produtos[key]}`)

        const data = await response.json()

        const prd = data.produto

        html += `
            <div class="cart-item">
              <img src="${prd.img_caminho}" alt="Product Image">
              <div class="product-description">
                  <p>${prd.prd_descricao}</p>
              </div>
             
              <div class="total-price">
                  R$<span class="result-price">${prd.prd_preco.replace('.', ',')}</span>
              </div>
              <button title="Adicionar ao Carrinho" class="cart-btn_wishlist" onclick="addCart(${prd.prd_codigo})"><i class="bi bi-cart-fill"></i></button>
              <button title="Excluir dos Favoritos" class="remove-btn" onclick="removerWishlist(${prd.prd_codigo})"><i class="bi bi-trash-fill"></i></button>
            </div>
        `

        totalWishlist += parseFloat(prd.prd_preco)

    }

    document.querySelector('#div_itens_wishlist').innerHTML = html
    document.querySelector('#preco_total_wishlist').textContent = String(totalWishlist).replace('.', ',')

}

function removerWishlist(prd_codigo){

    let produtos = JSON.parse(localStorage.getItem('wishlist')) || {}

    delete produtos[prd_codigo]

    localStorage.setItem('wishlist', JSON.stringify(produtos))

    countWishlist()
    carregarProdutosWishlist()

}
<<<<<<< HEAD
=======

async function loginUser(){
    const login = document.querySelector('#user_login').value

    if(String(login).trim() === ""){
        alert("Informe o login!")
        return
    }

    const password = document.querySelector('#user_password').value

    if(String(password).trim() === ""){
        alert("Informe a senha!")
        return
    }

    const data = {
        login: login,
        password: password
    }

    const url = "/login"

    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }

    const response = await fetch(url, options)

    if(response.ok){
        window.open('/', '_self')
    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }
}

async function cadastrarUsuario(){

    const erro = validar_campos('user_nome_cad', 'user_login_cad', 'user_senha_cad');

    if(erro === 1){
        return
    }

    limpar_validacao();

    const ps_nome    = document.querySelector('#user_nome_cad').value
    const user_login = document.querySelector('#user_login_cad').value
    const user_senha = document.querySelector('#user_senha_cad').value
    const user_admin = 0

    const url = '/users/create'

    const data = {
        ps_nome     : ps_nome,
        user_senha  : user_senha,
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
        alert("Usuário cadastrado! Faça o login.")
        window.open('/login', '_self')
    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }

}

async function addCart(prd_codigo){
    const url = '/addCart'

    const data = {
        prd_codigo : prd_codigo
    }

    const options = {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    }

    await fetch(url, options)
}
>>>>>>> ffbe0ad00a15249e2d543d2bf21bc2d16ae360b1
