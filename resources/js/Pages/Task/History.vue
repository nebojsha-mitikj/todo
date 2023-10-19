<script setup>
import {Head} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TaskTable from '@/Components/TaskTable.vue';
import {reactive, ref, computed} from 'vue';
import moment from 'moment/moment.js';

const props = defineProps({
    tasks: {
        type: Array,
        default: () => [],
    },
});

const data = reactive({
    tasks: ref(props.tasks)
});

const filteredTasks = computed(() => {
    let filtered = {}
    data.tasks.forEach((task) => {
        if (filtered[task.date] == null) {
            filtered[task.date] = []
        }
        filtered[task.date].push(task);
    });
    return filtered;
});

</script>

<template>
    <Head title="History"/>

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white">
                    <h2 class="mx-6 sm:mt-6 text-center text-lg font-medium text-gray-900">
                        Your history
                    </h2>
                    <div class="max-w-3xl mx-auto my-8" v-for="(tasks, date) in filteredTasks">
                        <p class="text-center my-2 text-sm text-gray-500">
                            {{moment(date, 'YYYY-MM-DD').format('dddd')}} {{ date }}
                        </p>
                        <TaskTable
                            :tasks="tasks"
                            :exclude="['edit', 'delete']"
                        ></TaskTable>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
