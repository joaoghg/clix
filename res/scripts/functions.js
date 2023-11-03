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

// botao Novo Endereco

const botaoMostrar = document.getElementById('mostrar-caixa');
        const caixaDialogo = document.getElementById('caixa-dialogo');
        const botaoFechar = document.getElementById('fechar-caixa');

        botaoMostrar.addEventListener('click', () => {
            caixaDialogo.style.display = 'block';
        });

        botaoFechar.addEventListener('click', () => {
            caixaDialogo.style.display = 'none';
        });

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