<script setup>
    import { ref, useTemplateRef } from 'vue';
    import axios from 'axios';
    import { useRouter } from 'vue-router';

    const router = useRouter();

    const errorMessage = useTemplateRef("errorMessage");

    const username = ref("");
    const password = ref("");
    const repeatedPassword = ref("");
    const token = ref("");
    const registration = ref(false);
    const remember = ref(false);

    if (localStorage.getItem("autologin")) {
        const loginData = JSON.parse(new TextDecoder().decode(Uint8Array.from(window.atob(localStorage.getItem("autologin")).split(","))));
        router.push("/main/" + loginData.username + "/" + loginData.password);
    }

    function login(e) {
        e.preventDefault();
        if (username.value && password.value) {
            axios.post("php/login.php", {
                username: username.value,
                password: password.value
            }).then((response) => {
                if (response.data == "success") {
                    if (remember.value) {
                        localStorage.setItem("autologin", window.btoa(new TextEncoder().encode(JSON.stringify({username: username.value, password: password.value}))));
                    }
                    router.push("/main/" + username.value + "/" + password.value);
                } else if (response.data) {
                    errorMessage.value.innerText = response.data;
                } else {
                    errorMessage.value.innerText = "An error occured.";
                }
            }).catch(() => {
                errorMessage.value.innerText = "An error occured.";
            })
        } else {
            errorMessage.value.innerText = "Please input a username and a password.";
        }
    }

    function register(e) {
        e.preventDefault();
        if (username.value && password.value && token.value && password.value == repeatedPassword.value && password.value.length >= 6 && username.value.length <= 255) {
            axios.post("php/register.php", {
                username: username.value,
                password: password.value,
                token: token.value
            }).then((response) => {
                if (response.data == "success") {
                    router.push("/main/" + username.value + "/" + password.value);
                } else if (response.data) {
                    errorMessage.value.innerText = response.data;
                } else {
                    errorMessage.value.innerText = "An error occured.";
                }
            }).catch(() => {
                errorMessage.value.innerText = "An error occured.";
            })
        } else {
            if (!username.value || !password.value || !repeatedPassword.value || !token.value) {
                errorMessage.value.innerText = "Please input a username, a password and the registration token.";
            } else if (password.value.length < 6) {
                errorMessage.value.innerText = "The password has to be at least 6 characters long.";
            } else if (username.value.length > 255) {
                errorMessage.value.innerText = "The username has to be 255 characters long at most.";
            } else{
                errorMessage.value.innerText = "The passwords don't match.";
            }
        }
    }

    function toggleRegistration(e) {
        e.preventDefault();
        registration.value = !registration.value;
        errorMessage.value.innerText = "";
        
        username.value = "";
        password.value = "";
        repeatedPassword.value = "";
        token.value = "";
    }
</script>

<template>
    <main id="loginMain">
        <form>
            <h1 id="loginText">
                {{ registration ? "Register" : "Login" }}
            </h1>
            <label>
                <p>Username</p>
                <input 
                    type="text"
                    v-model="username"
                >
            </label>

            <label>
                <p>Password</p>
                <input 
                    type="password"
                    v-model="password"
                >
            </label>

            <label v-if="registration">
                <p>Repeat password</p>
                <input 
                    type="password"
                    v-model="repeatedPassword"
                >
            </label>

            <label v-if="registration">
                <p>Registration token</p>
                <input 
                    type="password"
                    v-model="token"
                >
            </label>

            <label id="checkbox">
                <input 
                    type="checkbox"
                    v-model="remember"
                >
                <span>Remember me</span>
            </label>

            <p
                ref="errorMessage"
                id="errorMessage"    
            ></p>

            <section id="buttons">
                <button 
                    id="primaryButton"
                    @click="(e) => registration ? register(e) : login(e)"
                >
                    {{ registration ? "Register" : "Login"}}
                </button>

                <button 
                    id="secondaryButton"
                    @click="toggleRegistration"
                >
                    {{ registration ? "Login instead" : "Register instead" }}
                </button>
            </section>
        </form>
    </main>
</template>

<style>
    #loginMain {
        width: 100vw;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        color: rgb(32, 32, 32);
        background-image: linear-gradient(45deg, black, rgb(32, 32, 32));
    }

    form {
        background-color: white;
        border: 1px solid rgb(220, 220, 220);
        box-shadow: 0 0 8px 2px rgb(255, 255, 255, 0.3);
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 15px;
        width: 275px;
    }

    #errorMessage {
        color: red;
        margin: 0;
        text-align: center;
    }
    
    input {
        background-color: white;
        border-radius: 20px;
        height: 34px;
        border: 1px solid rgb(220, 220, 220);
        text-align: center;
        color: rgb(32, 32, 32);
    }

    label {
        margin: 10px 0;
        font-weight: 600;
        width: 75%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    label > p {
        margin: 0;
        margin-bottom: 10px;
    }

    #checkbox {
        flex-direction: row;
        margin: 0;
        width: fit-content;
        cursor: pointer;
    }

    #checkbox > input {
        cursor: pointer;
    }

    #checkbox > span {
        margin-left: 5px;
        font-weight: 400;
        user-select: none;
    }

    #buttons {
        display: flex;
        margin-top: 10px;
    }
    
    button {
        cursor: pointer;
        font-family: Arial, Helvetica, sans-serif;
    }

    #primaryButton {
        background-color: white;
        border-radius: 20px;
        margin-right: 5px;
        border: 1px solid rgb(220, 220, 220);
        color: rgb(32, 32, 32);
        font-weight: 600;
        width: 150px;
        height: 40px;
    }

    #primaryButton:hover {
        background-color: rgb(220, 220, 220);
    }

    #secondaryButton {
        background-color: white;
        border: none;
        font-weight: 300;
        color: rgb(120, 120, 120);
    }

    #loginText {
        margin-top: 7.5px;
    }

    @media (max-width: 400px) or (max-height: 600px) {
        form {
            border: none;
            box-shadow: none;
            height: 100vh;
            width: 100vw;
            justify-content: center;
            padding: 0;
            border-radius: 0;
        }
    }
</style>