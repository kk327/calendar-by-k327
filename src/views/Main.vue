<script setup>
    import { ref, computed } from 'vue';
    import axios from 'axios';
    import { useRoute } from 'vue-router';
    import AppHeader from '../components/AppHeader.vue'
    import Tile from '../components/Tile.vue'
    import NotePanel from '../components/NotePanel.vue'
    import calendarConfig from '../calendarConfig.json';

    const currentDate = new Date();
    const sundayFirst = calendarConfig.sundayFirst;

    const route = useRoute();
    const username = route.params.username;
    const password = route.params.password;

    const month = ref(currentDate.getMonth());
    const year = ref(currentDate.getFullYear());
    const editingANote = ref(false);
    const editedNote = ref(0);
    const dateEditionPopupVisible = ref(false);
    const notes = ref([]);
    const arrowKeyCooldown = ref(false);
    const loading = ref(true);
    const error = ref(false);

    const months = [
        { "name": "January", "length": 31 },
        { "name": "February", "length": 28 },
        { "name": "March", "length": 31 },
        { "name": "April", "length": 30 },
        { "name": "May", "length": 31 },
        { "name": "June", "length": 30 },
        { "name": "July", "length": 31 },
        { "name": "August", "length": 31 },
        { "name": "September", "length": 30 },
        { "name": "October", "length": 31 },
        { "name": "November", "length": 30 },
        { "name": "December", "length": 31 }
    ];

    function getMonthLength(month) {
        if (month == 1 && year.value % 4 == 0 && ((year.value % 100 != 0) || (year.value % 100 == 0 && year.value % 400 == 0))) {
            return 29;
        } else {
            return months[month].length;
        }
    }

    const firstWeekDay = computed(() => {
        return new Date(year.value, month.value, sundayFirst ? 1 : 0).getDay();
    });

    function onNoteEdit(index) {
        editingANote.value = true;
        editedNote.value = index;
        dateEditionPopupVisible.value = false;
    }

    function containsNote(day) {
        for (let note of notes.value) {
            if (note.year == year.value && note.month == month.value && note.day == day) {
                return note;
            }
        }
        return "";
    }

    function findNote() {
        return notes.value.find(e => e.year == year.value && e.month == month.value && e.day == editedNote.value);
    }

    function onNoteSave(color, content, toDatabase) {
        if (findNote()) {
            notes.value.splice(notes.value.indexOf(findNote()), 1);
        }

        if (content.trim()) {
            notes.value.push({year: year.value, month: month.value, day: editedNote.value, color: color, content: content.trim()});
        }
        
        if (toDatabase) {
            axios.post("php/save.php", {
                date: year.value + "-" + (month.value + 1) + "-" + (editedNote.value + 1),
                content: content.trim(),
                color: color,
                username: username,
                password: password
            }).catch(() => {
                error.value = true;
            });
        }
    }

    function changeMonth(value) {
        dateEditionPopupVisible.value = false;

        if (month.value == 0 && value == -1) {
            if (year.value != 0) {
                year.value--;
                month.value = 11;
            }
        } else if (month.value == 11 && value == 1) {
            if (year.value != 9999) {
                year.value++;
                month.value = 0;
            }
        } else {
            month.value += value;
        }
    }

    async function loadNotes() {
        axios.post("php/load.php", {
            username: username,
            password: password
        }).then((response) => {
            for (let object of response.data) {
                for (let newLineIndex of object.newLines) {
                    object.content = object.content.slice(0, newLineIndex) + "\n" + object.content.slice(newLineIndex);
                }
                delete object.newLines;
            }
            notes.value = response.data;
            loading.value = false;
        }).catch(() => {
            error.value = true;
            loading.value = false;
            localStorage.removeItem("autologin");
        })
    }
    loadNotes();
    
    addEventListener("keydown", (e) => {
        if (!arrowKeyCooldown.value && !editingANote.value && (e.key == "ArrowLeft" || e.key == "ArrowRight")) {
            if (e.key == "ArrowLeft") {
                changeMonth(-1);
            } else {
                changeMonth(1);
            }

            arrowKeyCooldown.value = true;
            addEventListener("keyup", (e2) => { 
                if (e.key == e2.key) {
                    arrowKeyCooldown.value = false;
                }
            });
        }
    })
</script>

<template>
    <section 
        id="loadingOrError"
        v-if="loading || error" 
    >
        <h1>
            {{ loading ? "Loading. Please wait." : "An error occured." }}
        </h1>
    </section>

    <AppHeader 
        v-if="!loading && !error"
        :style="{ '--rows': firstWeekDay + getMonthLength(month) > 35 ? 6 : firstWeekDay + getMonthLength(month) == 28 ? 4 : 5}" 
        :month="month" 
        :months="months" 
        :year="year"
        :dateEditionPopupVisible="dateEditionPopupVisible"
        :sundayFirst="sundayFirst"
        :editingANote="editingANote"
        @setYear="(newYear) => year = newYear" 
        @setMonth="(newMonth) => month = parseInt(newMonth)" 
        @changeMonth="(value) => changeMonth(value)"
        @changePopupVisibility="(visibility) => dateEditionPopupVisible = visibility"
    />

    <main
        id="mainMain" 
        v-if="!loading && !error"
        :style="{ '--rows': firstWeekDay + getMonthLength(month) > 35 ? 6 : firstWeekDay + getMonthLength(month) == 28 ? 4 : 5}" 
    >
        <Tile
            v-for="i of Array(firstWeekDay).keys()"
            v-if="firstWeekDay != 0"
            :dayNumber="(month == 0 ? 31 : getMonthLength(month - 1)) - firstWeekDay + i + 1"
            disabled
        />
        <Tile
            v-for="i of Array(getMonthLength(month)).keys()" 
            :style="{ backgroundColor: containsNote(i).color,
                      border: currentDate.getDate() == i + 1 && currentDate.getMonth() == month && currentDate.getFullYear() == year ? '3px solid red' : ''}"
            :dayNumber="i + 1" 
            :title="containsNote(i).content"
            :tabIndex="editingANote ? -1 : ''"
            @editNote="(index) => onNoteEdit(index)"
        />
        <Tile
            v-for="i of Array(7 - (firstWeekDay + getMonthLength(month)) % 7).keys()"
            v-if="(firstWeekDay + getMonthLength(month)) % 7 != 0"
            :dayNumber="i + 1"
            disabled
        />
    </main>

    <NotePanel
        v-if="editingANote"
        @quitNotePanel="editingANote = false"
        @save="(color, content, toDatabase) => onNoteSave(color, content, toDatabase)"
        :color="findNote() ? findNote().color : '#ffb4b4'"
        :content="findNote() ? findNote().content : ''"
    />
</template>

<style>
    body {
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        background-color: white;
        color: rgb(32, 32, 32);
    }

    #app {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #mainMain {
        width: calc((100vh - 100px) / var(--rows) * 7);
        max-width: 100vw;
        aspect-ratio: 7 / var(--rows);
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    #loadingOrError {
        height: 100vh;
        width: 100vw;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 12.5px;
        box-sizing: border-box;
    }
</style>