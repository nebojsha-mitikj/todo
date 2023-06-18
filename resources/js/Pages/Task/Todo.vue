<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {reactive, ref} from "vue";

const ENTER_KEY = 13

const availableStatuses = [
    'to-do',
    'in-progress',
    'finished'
];

const taskInput = ref('')

const form = useForm({
    task: '',
});

const todo = reactive({
    tasks: [],
    editedTaskIndex: null
});

const deleteTask = (index) => {
    todo.tasks.splice(index, 1);
};

const editTask = (index) => {
    form.task = todo.tasks[index].name
    todo.editedTaskIndex = index;
    taskInput.value.focus()
};

const changeStatus = (index) => {
    let statusIndex = availableStatuses.indexOf(todo.tasks[index].status);
    todo.tasks[index].status = availableStatuses[++statusIndex % availableStatuses.length]
};

const handleInputKeyPress = (event) => {
    if (event.keyCode === ENTER_KEY) {
        submit();
    }
};

const submit = () => {
    if(form.task.length === 0){
        return;
    }
    if(todo.editedTaskIndex == null){
        todo.tasks.push({
            'name': form.task,
            'status': 'to-do'
        });
    }else{
        todo.tasks[todo.editedTaskIndex].name = form.task;
        todo.editedTaskIndex = null;
    }
    form.task = '';
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

                    <h2 class="m-6 text-center text-lg font-medium text-gray-900">Your tasks for today</h2>

                    <div class="flex bg-red-100 max-w-3xl mx-auto">
                        <TextInput
                            @keyup="handleInputKeyPress"
                            ref="taskInput"
                            id="task"
                            type="text"
                            class="w-3/4"
                            placeholder="Enter task"
                            v-model="form.task"
                            required
                        />
                        <PrimaryButton
                            class="justify-center w-1/4"
                            :disabled="form.processing"
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
                                <th class="px-6 py-3 bg-gray-50 w-48 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                <tr v-for="(task, index) in todo.tasks">
                                    <td class="px-6 py-4">
                                        <span :class="{'line-through': task.status === 'finished'}">
                                            {{ task.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <label
                                            @click="changeStatus(index)"
                                            class="cursor-pointer select-none"
                                            :class="{
                                                'text-red-600': task.status === 'to-do',
                                                'text-yellow-600': task.status === 'in-progress',
                                                'text-green-600': task.status === 'finished'
                                            }"
                                        >
                                            {{ task.status }}
                                        </label>
                                    </td>
                                    <td class="px-6 py-4">
                                        <font-awesome-icon
                                            class="cursor-pointer"
                                            icon="edit"
                                            @click="editTask(index)"
                                        />
                                    </td>
                                    <td class="px-6 py-4">
                                        <font-awesome-icon
                                            class="cursor-pointer"
                                            icon="trash"
                                            @click="deleteTask(index)"
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
