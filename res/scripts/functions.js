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

function filtrarProdutos(){

    const formData = new FormData()

    document.querySelectorAll('.chkCat:checked').forEach(function(chk){

        formData.append('categorias[]', chk.value)

    })

    const options = {
        method: "POST",
        body: formData
    }

    const url = "/store";

    fetch(url, options)

}

