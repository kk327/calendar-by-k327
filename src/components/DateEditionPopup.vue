<script setup>
    const props = defineProps([
        "year",
        "months",
        "month"
    ])

    const emit = defineEmits([
        "changeYear"
    ])

    function onYearChange(value) {
        if (value >= 0 && value <= 9999) {
            emit("changeYear", value);
        } else if (value > 9999) {
            emit("changeYear", 9999);
        } else {
            emit("changeYear", 0);
        }
    }
</script>

<template>
    <section>
        <label>
            <p>Year</p>
            <input 
                type="number" 
                id="year" 
                min="0"
                max="9999"
                :value="year" 
                @change="(e) => onYearChange(parseInt(e.target.value))"
            >
        </label>
        
        <label>
            <p>Month</p>
            <select 
                id="month"
                @change="(e) => $emit('changeMonth', e.target.value)"
            >
                <option 
                    v-for="(m, index) of months" 
                    :value="index" 
                    :selected="months[month] == m"
                >{{ m.name }}</option>
            </select>
        </label>
    </section>
</template>

<style scoped>
    section {
        width: 200px;
        max-height: calc(100vh - 80px);
        background-color: white;
        border: 1px solid rgb(220, 220, 220);
        box-shadow: 0 0 8px 2px rgb(0, 0, 0, 0.3);
        border-radius: 20px;
        position: fixed;
        top: 65px;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 99;
    }

    input, select {
        background-color: white;
        border-radius: 20px;
        height: 34px;
        border: 1px solid rgb(220, 220, 220);
        text-align: center;
        width: 100%;
        color: rgb(32, 32, 32);
    }

    select {
        cursor: pointer;
        margin-bottom: 15px;
    }

    label {
        margin: 10px 0;
        font-weight: 600;
        width: 75%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    label:last-child {
        margin-bottom: 0;
    }

    p {
        margin: 0;
        margin-bottom: 10px;
    }
</style>