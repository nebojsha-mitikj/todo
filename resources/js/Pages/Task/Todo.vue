<script setup>
import {Head} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TaskTable from '@/Components/TaskTable.vue';
import axios from 'axios';
import {reactive, ref, computed, toRaw} from 'vue';
import { getStringFromTimeObject, getTimeObjectFromString } from "@/Utils/TimeUtil.js";
import useEmitter from '@/Composables/useEmitter.js';

const ENTER_KEY = 13;

const STATUSES = ['to-do', 'in-progress', 'finished'];

const emitter = useEmitter();

const props = defineProps({
    tasks: {
        type: Array,
        default: () => [],
    },
    today: {
        type: String
    }
});

const taskInput = ref('')

const data = reactive({
    tasks: ref(props.tasks),
    taskDescription: '',
    taskTimeRange: ref(),
    editedTaskId: null,
    displayPlanner: false
});


const filteredTasks = computed(() => {
    let today = ref(props.today).value;
    return data.tasks.filter((task) => {
        return data.displayPlanner ? task.date !== today : task.date === today;
    })
});

const handleInputKeyPress = (event) => {
    if (event.keyCode === ENTER_KEY) {
        submit();
    }
};

const getTaskIndexById = (id) => {
    for(let index = 0; index < data.tasks.length; index++){
        if(data.tasks[index].id === id){
            return index;
        }
    }
    throw new Error('Task with id ' + id + ' does not exist.');
}

const editTask = (id) => {
    let index = getTaskIndexById(id)
    data.taskDescription = data.tasks[index].description
    data.editedTaskId = data.tasks[index].id;
    data.taskTimeRange = ref([
        getTimeObjectFromString(data.tasks[index].start_time),
        getTimeObjectFromString(data.tasks[index].end_time)
    ]);
    taskInput.value.focus()
};


const switchDisplay = () => {
    data.editedTaskId = null;
    data.taskDescription = '';
    data.taskTimeRange = ref();
    data.displayPlanner = !data.displayPlanner;
}

const changeTaskStatus = (id) => {
    let index = getTaskIndexById(id)
    let newStatusIndex = (STATUSES.indexOf(data.tasks[index].status) + 1) % STATUSES.length;
    axios.put(route('task.update.status', {task: data.tasks[index].id}), {
        'status': STATUSES[newStatusIndex]
    }).then(res => {
        data.tasks[index] = res.data.task;
        if(res.data.goalReached){
            emitter.emit('goalReached');
        }
    });
};

const deleteTask = (id) => {
    axios.delete(route('task.destroy', {task: id})).then(res => {
        if(res.data.taskDestroyed){
            data.tasks.splice(getTaskIndexById(id), 1);
        }
    });
};

const storeTask = () => {
    let taskData = {
        'description': data.taskDescription,
        'status': 'to-do',
        'for_today': !data.displayPlanner,
        'start_time': getStringFromTimeObject(data.taskTimeRange[0]),
        'end_time': getStringFromTimeObject(data.taskTimeRange[1])
    }
    axios.post(route('task.store'), taskData).then(res => {
        data.tasks.push(res.data.task);
        data.taskDescription = '';
        data.taskTimeRange = ref();
    })
}

const updateTask = () => {
    let index = getTaskIndexById(data.editedTaskId)
    let taskData = {
        'description': data.taskDescription,
        'status': data.tasks[index].status,
        'start_time': getStringFromTimeObject(data.taskTimeRange[0]),
        'end_time': getStringFromTimeObject(data.taskTimeRange[1])
    }
    axios.put(route('task.update', {task: data.tasks[index].id}), taskData).then(res => {
        data.tasks[index] = res.data.task;
        data.taskDescription = '';
        data.taskTimeRange = ref();
        data.editedTaskId = null;
    });
}

const submit = () => {
    if(data.taskDescription.length === 0 || toRaw(data.taskTimeRange) == null){
        return;
    }
    if(data.editedTaskId == null){
        storeTask()
    }else{
        updateTask()
    }
}

</script>

<template>
    <Head title="Planner" />

    <AuthenticatedLayout>

        <div class="sm:py-12 py-6">
            <div class="max-w-7xl mx-auto lg:px-8 sm:px-6 px-2">
                <div class="bg-white">

                    <h2 class="mx-6 sm:mt-6 text-center text-lg font-medium text-gray-900">
                        Your tasks for {{!data.displayPlanner ? 'today' : 'tomorrow'}}
                    </h2>

                    <div class="max-w-3xl mx-auto text-right">
                        <p class="text-gray-500 select-none inline-block cursor-pointer mt-3 mb-4 text-sm">
                            <span @click="switchDisplay">
                                {{!data.displayPlanner ? 'Switch to Planner' : 'Back to Today'}}
                            </span>
                        </p>
                    </div>

                    <div class="grid grid-cols-12 max-w-3xl mx-auto">
                        <TextInput
                            @keyup="handleInputKeyPress"
                            ref="taskInput"
                            id="task"
                            type="text"
                            class="sm:col-span-6 col-span-12 h-10"
                            placeholder="Enter task"
                            v-model="data.taskDescription"
                            required
                        />
                        <VueDatePicker
                            v-model="data.taskTimeRange"
                            class="sm:col-span-3 col-span-12 sm:mt-0 mt-2"
                            placeholder="Enter time"
                            time-picker
                            disable-time-range-validation
                            range
                        />
                        <PrimaryButton
                            class="h-10 w-full block-important sm:col-span-3 col-span-12 sm:mt-0 mt-2"
                            @click="submit"
                        >
                            Submit
                        </PrimaryButton>
                    </div>

                    <div class="max-w-3xl mx-auto my-8">
                        <TaskTable
                            :tasks="filteredTasks"
                            :exclude="data.displayPlanner ? ['status'] : []"
                            @editTask="editTask"
                            @deleteTask="deleteTask"
                            @changeTaskStatus="changeTaskStatus"
                        ></TaskTable>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.dp__action_select, .dp__action_select:hover {
    background: #1f2937;
}
.dp__action_cancel, .dp__action_select {
    padding: 5px 10px;
    height: auto;
}
.dp__action_cancel:hover {
    border-color: #1f2937;
}
.dp__selection_preview {
    display: none;
}
.dp__input_readonly {
    height: 2.5rem;
}
.block-important {
    display: block !important;
}
</style>
