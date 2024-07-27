let passwordFirstInput = document.querySelector('#user_password_first')
let passwordSecondInput = document.querySelector('#user_password_second')
let signupForm = document.querySelector('#signup-form')
let errorZone = document.querySelector('#error-zone');

signupForm.addEventListener('submit', function (event) {

    event.preventDefault();
    let passwordFirst = passwordFirstInput.value;
    let passwordSecond = passwordSecondInput.value;


    if (passwordFirst ==! passwordSecond) {

        printMessage('Les deux mots ne sont pas identique');
        return false;


    } else if (!containsNumbers(passwordFirst) || !(passwordFirst.length >= 8) || !containsUppercase(passwordFirst) || !containsLowercase(passwordFirst) || !containsSpecialCharacters(passwordFirst)) {
    
        printMessage('Le mot de passe doit contenir au moins 8 caractères avec au mins une majuscule, une minusle, un chiffre et un des caractères spéciaux suivant : ~#{[|`\\^@]`]}', 'warning');        
        return false;

    } else {
        this.submit();

    }

});

function printMessage(message, status){

    errorZone.innerHTML = "<div class='alert alert-"+status+"'>"+message+"</div>"

}

function containsUppercase(str) {
    const regex = /[A-Z]/;
    return regex.test(str);
}

function containsLowercase(str) {
    const regex = /[a-z]/;
    return regex.test(str);
}

function containsSpecialCharacters(str) {
    const regex = /[~#\{\[\|\`\\\^@}]|\]/;
    return regex.test(str);
}



function containsNumbers(str) {
    const regex = /\d/;
    return regex.test(str);
}
