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