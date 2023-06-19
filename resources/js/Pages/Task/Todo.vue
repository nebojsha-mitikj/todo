<script setup>
import {Head} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import axios from 'axios';
import {reactive, ref, computed} from 'vue';
import moment from 'moment';

const ENTER_KEY = 13;

const STATUSES = [
    'to-do',
    'in-progress',
    'finished'
];

const props = defineProps({
    tasks: {
        type: Array,
        default: () => [],
    },
});

const taskInput = ref('')

const data = reactive({
    tasks: ref(props.tasks),
    taskDescription: '',
    editedTaskId: null,
    displayPlanner: false
});

const filteredTasks = computed(() => {
    return data.tasks.filter((task) => {
        return task.date === moment()
            .utcOffset('+0200')
            .add(Number(data.displayPlanner),'days')
            .format('YYYY-MM-DD')
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

const firstCharToUpperCase = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

const editTask = (id) => {
    let index = getTaskIndexById(id)
    data.taskDescription = data.tasks[index].description
    data.editedTaskId = data.tasks[index].id;
    taskInput.value.focus()
};

const switchDisplay = () => {
    data.editedTaskId = null;
    data.taskDescription = '';
    data.displayPlanner = !data.displayPlanner;
}

const changeTaskStatus = (id) => {
    let index = getTaskIndexById(id)
    let newStatusIndex = (STATUSES.indexOf(data.tasks[index].status) + 1) % STATUSES.length;
    let taskData = {
        'description': data.tasks[index].description,
        'status': STATUSES[newStatusIndex],
        'date': data.tasks[index].date
    }
    axios.put(route('task.update', {task: data.tasks[index].id}), taskData).then(res => {
        data.tasks[index] = res.data.task;
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
        'date': moment()
            .utcOffset('+0200')
            .add(Number(data.displayPlanner),'days')
            .format('YYYY-MM-DD')
    }
    axios.post(route('task.store'), taskData).then(res => {
        data.tasks.push(res.data.task);
        data.taskDescription = '';
    })
}

const updateTask = () => {
    let index = getTaskIndexById(data.editedTaskId)
    let taskData = {
        'description': data.taskDescription,
        'status': data.tasks[index].status,
        'date': data.tasks[index].date
    }
    axios.put(route('task.update', {task: data.tasks[index].id}), taskData).then(res => {
        data.tasks[index] = res.data.task;
        data.editedTaskId = null;
        data.taskDescription = '';
    });
}

const submit = () => {
    if(data.taskDescription.length === 0){
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
    <Head title="Todo" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Todo</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                    <h2 class="mx-6 mt-6 text-center text-lg font-medium text-gray-900">
                        Your tasks for {{!data.displayPlanner ? 'today' : 'tomorrow'}}
                    </h2>

                    <div class="max-w-3xl mx-auto text-right">
                        <p class="text-gray-500 select-none inline-block cursor-pointer mt-3 mb-4 text-sm">
                            <span @click="switchDisplay">
                                {{!data.displayPlanner ? 'Switch to Planner' : 'Back to Today'}}
                            </span>
                        </p>
                    </div>

                    <div class="flex bg-red-100 max-w-3xl mx-auto">
                        <TextInput
                            @keyup="handleInputKeyPress"
                            ref="taskInput"
                            id="task"
                            type="text"
                            class="w-3/4"
                            placeholder="Enter task"
                            v-model="data.taskDescription"
                            required
                        />
                        <PrimaryButton
                            class="justify-center w-1/4"
                            @click="submit"
                        >
                            Submit
                        </PrimaryButton>
                    </div>

                    <div class="max-w-3xl mx-auto my-8">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Task
                                </th>
                                <th
                                    v-if="!data.displayPlanner"
                                    class="px-6 py-3 bg-gray-50 w-48 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 bg-gray-50 w-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                                <th class="px-6 py-3 bg-gray-50 w-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    #
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(task, index) in filteredTasks">
                                    <td class="px-6 py-4">
                                        <span :class="{'line-through': task.status === 'finished'}">
                                            {{ task.description }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4" v-if="!data.displayPlanner">
                                        <label
                                            @click="changeTaskStatus(task.id)"
                                            class="inline cursor-pointer select-none"
                                            :class="{
                                                'text-red-600': task.status === 'to-do',
                                                'text-yellow-600': task.status === 'in-progress',
                                                'text-green-600': task.status === 'finished'
                                            }"
                                        >
                                            {{ firstCharToUpperCase(task.status) }}
                                        </label>
                                    </td>
                                    <td class="px-6 py-4">
                                        <font-awesome-icon
                                            class="cursor-pointer"
                                            icon="edit"
                                            @click="editTask(task.id)"
                                        />
                                    </td>
                                    <td class="px-6 py-4">
                                        <font-awesome-icon
                                            class="cursor-pointer"
                                            icon="trash"
                                            @click="deleteTask(task.id)"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
