const userName = document.querySelector("#userName");
const userNameButton = document.querySelector("#userNameButton");
const inputOctaveCode = document.querySelector("#inputOctaveCode");
const inputOctaveCodeButton = document.querySelector("#inputOctaveCodeButton");

userNameButton.addEventListener('click', () => {
    console.log(userName.value);
})

inputOctaveCodeButton.addEventListener('click', () => {
    console.log(inputOctaveCode.value);
})