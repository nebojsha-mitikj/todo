<script setup>
import {FontAwesomeIcon} from '@fortawesome/vue-fontawesome';
import {reactive} from 'vue';

const props = defineProps({
    exclude: {
        type: Array,
        default: []
    },
    tasks: {
        type: Array,
        default: []
    }
});

const data = reactive({
    baseStyle: 'px-6 py-3 bg-gray-50 text-left text-xs ' +
        'font-medium text-gray-500 uppercase tracking-wider'
});

const firstCharToUpperCase = (str) => {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

</script>

<template>
    <table class="min-w-full divide-y divide-gray-200">
        <thead>
        <tr>
            <th v-if="!exclude.includes('description')" :class="data.baseStyle">Task</th>
            <th v-if="!exclude.includes('status')" :class="data.baseStyle + ' w-48'">Status</th>
            <th v-if="!exclude.includes('edit')" :class="data.baseStyle + ' w-4'">#</th>
            <th v-if="!exclude.includes('delete')" :class="data.baseStyle + ' w-4'">#</th>
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="task in tasks">
                <td class="px-6 py-4" v-if="!exclude.includes('description')">
                    <span :class="{'line-through': task.status === 'finished'}">
                        {{ task.description }}
                    </span>
                </td>
                <td class="px-6 py-4" v-if="!exclude.includes('status')">
                    <label
                        @click="$emit('changeTaskStatus', task.id)"
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
                <td class="px-6 py-4" v-if="!exclude.includes('edit')">
                    <font-awesome-icon
                        class="cursor-pointer"
                        icon="edit"
                        @click="$emit('editTask', task.id)"
                    />
                </td>
                <td class="px-6 py-4" v-if="!exclude.includes('delete')">
                    <font-awesome-icon
                        class="cursor-pointer"
                        icon="trash"
                        @click="$emit('deleteTask', task.id)"
                    />
                </td>
            </tr>
        </tbody>
    </table>
</template>
