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
};


// Script formatacao Telefone
const handlePhone = (event) => {
let input = event.target
input.value = phoneMask(input.value)
}

const phoneMask = (value) => {
if (!value) return ""
value = value.replace(/\D/g, '')
value = value.replace(/(\d{2})(\d)/, "($1) $2")
value = value.replace(/(\d)(\d{4})$/, "$1-$2")
return value
}