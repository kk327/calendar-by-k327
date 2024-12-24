<script setup>
    import { ref, onMounted, watch } from 'vue';
    import DateEditionPopup from './DateEditionPopup.vue';
    import { useRouter } from 'vue-router';

    const router = useRouter();

    const calendarWidth = ref(0);

    const weekDays = [["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
                      ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"]];

    const props = defineProps([
        "month",
        "months",
        "year",
        "dateEditionPopupVisible",
        "sundayFirst",
        "editingANote"
    ]);

    const emit = defineEmits([
        "setMonth",
        "setYear",
        "hideDateEditionPopup",
        "changeMonth"
    ]);

    function setCalendarWidth() {
        calendarWidth.value = document.querySelector("main").clientWidth;
    }

    function logout() {
        localStorage.removeItem("autologin");
        router.push("/login");
    }

    addEventListener("resize", setCalendarWidth);
    onMounted(setCalendarWidth);
    watch(() => props.year + props.month, () => setTimeout(setCalendarWidth));
</script>

<template>
    <header>
        <button
            id="logout"
            title="Logout"
            @click="logout"
            :tabIndex="editingANote ? -1 : ''"
        ><img 
            src="@/assets/logout.png"
            alt="Logout"
        ></button>

        <section id="controls">
            <button 
                title="Previous month"
                @click="$emit('changeMonth', -1)" 
                :disabled="year == 0 && month == 0"
                :tabIndex="editingANote ? -1 : ''"
            ><img 
                src="@/assets/arrow.png" 
                alt="<">
            </button>

            <button 
                title="Month and year selection"
                id="navigationData" 
                @click="$emit('changePopupVisibility', !dateEditionPopupVisible)"
                :tabIndex="editingANote ? -1 : ''"
            >{{ months[month].name + " " + year }}</button>

            <button 
                title="Next month"
                @click="$emit('changeMonth', 1)" 
                :disabled="year == 9999 && month == 11"
                :tabIndex="editingANote ? -1 : ''"
            ><img 
                id="rightArrow"
                src="@/assets/arrow.png" 
                alt="<">
            </button>

            <DateEditionPopup 
                v-if="dateEditionPopupVisible" 
                :year="year" 
                :months="months" 
                :month="month" 
                @changeYear="(newYear) => $emit('setYear', newYear)" 
                @changeMonth="(newMonth) => $emit('setMonth', newMonth)" 
            />
        </section>

        <section id="weekDays">
            <p
                v-for="weekDay of weekDays[sundayFirst ? 1 : 0]"
                class="weekDayName"
            >{{ calendarWidth > 650 ? weekDay : weekDay.substring(0, 3) + "." }}</p>
        </section>
    </header>
</template>

<style scoped>
    header {
        display: flex;
        flex-direction: column;
        height: 100px;
        background-color: black;
    }

    #controls {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 60px;
        width: 100vw;
    }

    #weekDays {
        width: 100vw;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .weekDayName {
        text-align: center;
        font-weight: 600;
        width: calc((100vh - 100px) / var(--rows));
        color: white;
    }
    
    button {
        background-color: white;
        border-radius: 20px;
        margin: 0 7.5px;
        width: 35px;
        height: 35px;
        cursor: pointer;
        font-family: Arial, Helvetica, sans-serif;
        color: rgb(32, 32, 32);
        border: 1px solid rgb(220, 220, 220);
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 600;
    }

    button:hover {
        background-color: rgb(220, 220, 220);
    }

    button:disabled {
        background-color: rgb(200, 200, 200);
        cursor: default;
    }

    #navigationData {
        width: 150px;
        height: 40px;
    }

    #rightArrow {
        rotate: 180deg;
    }

    img {
        width: 17.5px;
        height: 17.5px;
    }

    #logout {
        width: 35px;
        height: 35px;
        position: fixed;
        top: 7.5px;
        left: -5px;
        background-color: rgb(0, 0, 0, 0);
        color: white;
        border: none;
        opacity: 75%;
        font-size: 100%;
    }

    #logout:hover {
        opacity: 100%;
    }

    #logout > img {
        width: 100%;
        height: 100%;
    }
</style>
