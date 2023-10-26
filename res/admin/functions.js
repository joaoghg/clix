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
    console.log(response)

    if(response.status === 200){
        window.open('/admin', '_self')
    }
    else{
        const erro = await response.json()
        alert(erro.msg)
    }
}