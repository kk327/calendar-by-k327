<script setup>
    import { useTemplateRef, onMounted, onUnmounted, onBeforeUnmount } from 'vue';

    const emit = defineEmits([
        "save",
        "quitNotePanel"
    ])
    
    const props = defineProps([
        "color",
        "content"
    ])

    const textarea = useTemplateRef("textarea");
    const colorInput = useTemplateRef("colorInput");

    function onClick(e) {
        if (textarea.value && document.activeElement != textarea.value) {
            if (document.activeElement != colorInput.value) {
                if (e.clientY > document.body.clientHeight / 2) {
                    textarea.value.setSelectionRange(textarea.value.value.length, textarea.value.value.length);
                } else {
                    textarea.value.setSelectionRange(0, 0);
                }
            }
            textarea.value.focus();
        }
    }

    function onKeydown(e) {
        if (e.key == "Escape") {
            emit('save', colorInput.value.value, textarea.value.value, true);
            emit("quitNotePanel");
        }
    }

    function setTextareaHeight() {
        textarea.value.style.height = "0px";
        textarea.value.style.height = textarea.value.scrollHeight + 4 + "px";
    }

    onMounted(() => {
        textarea.value.value = props.content;
        colorInput.value.value = props.color;

        addEventListener("keydown", onKeydown);
        setTimeout(addEventListener("click", onClick), 0);
        textarea.value.focus();

        setTextareaHeight();
        textarea.value.addEventListener("input", setTextareaHeight);
    });

    onBeforeUnmount(() => {
        textarea.value.removeEventListener("input", setTextareaHeight);
    })

    onUnmounted(() => {
        removeEventListener("click", onClick);
        removeEventListener("keydown", onKeydown);
    })
</script>

<template>
    <section id="notePanel">
        <button 
            title="Close"
            @click="$emit('quitNotePanel')"
        ><img 
            src="@/assets/close.png" 
            alt="X"
        ></button>
        
        <textarea 
            ref="textarea"
            @input="$emit('save', colorInput.value, textarea.value, false)"
            @change="$emit('save', colorInput.value, textarea.value, true)"
        ></textarea>

        <input 
            title="Note color"
            type="color" 
            ref="colorInput"
            @change="$emit('save', colorInput.value, textarea.value, true)"
        >
    </section>
</template>

<style scoped>
    #notePanel {
        position: fixed;
        width: 100vw;
        height: 100vh;
        background-color: rgb(0, 0, 0, 0.966);
        top: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: text;
    }

    input {
        border: 1px solid rgb(220, 220, 220);
        border-radius: 50px;
        width: 40px;
        height: 40px;
        position: fixed;
        top: 12.5px;
        left: 12.5px;
        cursor: pointer;
        background-color: white;
        padding: 0;
    }

    input::-moz-color-swatch {
        border: none;
        border-radius: 50%;
    }
    
    input::-webkit-color-swatch {
        border: none;
        border-radius: 50%;
        margin: -4px -2px;
    }

    button {
        background-color: white;
        border-radius: 20px;
        width: 40px;
        height: 40px;
        cursor: pointer;
        font-family: Arial, Helvetica, sans-serif;
        color: rgb(32, 32, 32);
        border: 1px solid rgb(220, 220, 220);
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 600;
        position: fixed;
        top: 12.5px;
        right: 12.5px;
    }

    button:hover {
        background-color: rgb(220, 220, 220);
    }

    input:hover {
        filter: brightness(0.9);
        border: 1px solid rgb(244, 244, 244);
    }

    textarea {
        width: 80vw;
        max-height: calc(100vh - 130px);
        outline: none;
        text-align: center;
        color: white;
        background-color: transparent;
        word-break: break-word;
        padding: 5px;
        font-family: Arial, Helvetica, sans-serif;
        box-sizing: border-box;
        resize: none;
        border: none;
        font-size: 100%;
    }

    img {
        width: 20px;
        height: 20px;
    }
</style>